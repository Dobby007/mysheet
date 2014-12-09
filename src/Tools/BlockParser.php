<?php

namespace MSSLib\Tools;

use MSSLib\Tools\IParser;
use MSSLib\Structure\Block;
use MSSLib\Structure\NodeBlock;
use MSSLib\Structure\Document;
use MSSLib\Error\ParseException;
use MSSLib\Essentials\ParserExtension;
use MSSLib\Essentials\ParserContext;
use MSSLib\Essentials\SourceLine;
use MSSLib\Essentials\SourceClosure;
use MSSLib\Traits\RootClassTrait;
use MSSLib\Helpers\StringHelper;

/**
 * Parser class is used to parse a MSS or CSS text into a document.
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class BlockParser implements IParser 
{
    use RootClassTrait;

    protected $extensions = array();
    
    private $code = null;
    private $sourceClosure = null;
    private $doc = null;
    protected $processedCode = null;
    
    public function __construct($code) {
        $this->setCode($code);
    }

    public function getCode() {
        return $this->code;
    }

    public function setCode($code) {
        $this->code = (string) $code;
    }

    public function addParserExtension($extension) {
        if (is_string($extension) && class_exists($extension)) {
            $extension = new $extension();
        }
        
        if ($extension instanceof ParserExtension) {
            $this->extensions[] = $extension;
        }
    }

    public function comeon() {
        $this->doc = new Document();
        $this->curBlock = $this->doc;
        $start_time = microtime(true);
        $this->processedCode = $this->getCode();
        $this->removeComments();
        $this->divideIntoLines();
        $end_time = microtime(true);
        echo "\nElapsed time 1:" . ($end_time - $start_time);
        
        $start_time = microtime(true);
        $context = new ParserContext($this, $this->sourceClosure);
        $parsedBlocks = $this->parseContext($context);
        foreach ($parsedBlocks as $parsedBlock) {
            $this->doc->addChild($parsedBlock);
        }
        $end_time = microtime(true);
        echo "\nElapsed time 2:" . ($end_time - $start_time);
        
        Debugger::logObjects("Document", $this->doc, "\n");
        return $this->doc;
    }

    protected function removeComments() {
        $this->processedCode = preg_replace(['#//[^\n]*#', '#/\*.*\*/#Us'], '', $this->processedCode);
    }
    
    protected function divideIntoLines() {
        $this->lines = preg_split("/\n|\r\n/", $this->processedCode);

        $indentSize = null;
        $tabsInsteadSpaces = false;
        /* @var $lines SourceLine[] */
        $lines = [];
        $curly_blocks = [];
        $blockTree = [];
        
        $rootBlock = (new SourceClosure())->addChildClosure(new SourceClosure());
        /* @var $curBlock SourceClosure */
        $curBlock = $rootBlock->getChildClosure(-1);
        
        array_walk($this->lines, function($line, $index) 
            use(&$indentSize, &$tabsInsteadSpaces, &$lines, &$curly_blocks, &$curBlock, &$rootBlock) 
        {
            /* @var $curBlock SourceClosure */
            if ($indentSize === null && substr($line, 0, 1) === "\t") {
                $tabsInsteadSpaces = true;
            }
            $spacesCount = self::countLineSpaces($line, $tabsInsteadSpaces, $indentSize);
            if ($spacesCount === false) {
                return;
            }
            
            $lineLevel = $indentSize > 0 ? intval($spacesCount / $indentSize) : 0;
            $lineLevel++;
            
            if ($lineLevel > $curBlock->getLevel()) {
                $lineLevel = $curBlock->getLevel() + 1;
            }
            
            if (count($curly_blocks) > 0 && $lineLevel <= end($curly_blocks)) {
                $lineLevel = end($curly_blocks) + 1;
            }
            
            $newLevel = $lineLevel;
            $len = strlen($line);
            $offset = $spacesCount;
            $i = $offset;
            $handled = false;
            
            Debugger::logString($lineLevel . ': ' . $line);
            $curBlock = $this->findAppropriateClosure($curBlock, $lineLevel );
            while ($i < $len) {
                if ($line[$i] === '{') {
                    $newLine = trim(substr($line, $offset, $i - $offset));
                    //if there is a string before this curly bracket
                    if (!empty($newLine)) {
                        if ($newLevel === $lineLevel && $handled) {
                            //we need to find where to put it
                            $curBlock = $this->findAppropriateClosure($curBlock, $lineLevel );
                        }
                        $curBlock->addLine($newLine);
                    }
                    $curly_blocks[] = $newLevel;
                    $newLevel++;
                    $curBlock = $this->addChildToClosure($curBlock);
                    $offset = $i + 1;
                    $handled = true;
                } else if ($line[$i] === '}' && count($curly_blocks) > 0) {
                    $newLine = trim(substr($line, $offset, $i - $offset));
                    //we need to add string before this curly bracket to the current closure
                    if (!empty($newLine)) {
                        $curBlock->addLine($newLine);
                    }
                    array_pop($curly_blocks);
//                    if (empty($curly_blocks)) break;
                    //also we need to find a proper closure for a next line
//                    $curBlock = $this->getParentClosureForLevel($curBlock, $newLevel);
                    $newLevel--;
                    $offset = $i + 1;
                    $handled = true;
                }
                $i++;
            }
            
            if ($handled) {
                $endstr = trim(substr($line, $offset, $i - $offset));
                if (!empty($endstr)) {
                    $curBlock->addLine($endstr);
                }
            } else {
//                $curBlock = $this->findAppropriateClosure($curBlock, $lineLevel);
                $curBlock->addLine(rtrim(substr($line, $spacesCount)));
            }
        });
        
        while ($curBlock->getParent()) {
            $curBlock = $curBlock->getParent();
        }
        Debugger::logString("\n\n-==LINES==-\n" . $curBlock . "\n===\n\n");
//        Debugger::logObjects("As Object:", $curBlock, "===\n");
        $this->sourceClosure = $curBlock;
    }
    
    public function parseContext(ParserContext $context) {
        $this->curLine = 0;
        $curLine = $context->curLine();
        if (!$curLine) {
            return [];
        }
        //we need to create a temp document to insert parsed blocks in it
        $rootBlock = new Document();
        $curBlock = $rootBlock;
        
        do {
            $result = false;
            $curBlockDepth = $curBlock->getDepth();
            $curLineLevel = $context->curLine()->getLevel() - 1;
            if ($curBlockDepth > $curLineLevel) {
                while ($curBlock->getDepth() > $curLineLevel) {
                    $curBlock = $curBlock->getParent();
                    if ($curBlock === null) {
                        throw new ParseException(null, 'PARENT_NOT_FOUND');
                    }
                }
            }
            
            foreach ($this->extensions as $extension) {
                $result = $this->tryParse($context, $extension);
                if ($result !== false) {
                    break;
                }
                $context->restoreCursorState();
            }
            
            if (
                $curBlock instanceof NodeBlock && 
                $result instanceof Block
            ) {
                $curBlock->addChild($result);
            } else if (!($result instanceof Block)) {
                throw new ParseException(null, 'UNRECOGNIZED_SEQUENCE', [$context->curLine()->getLine()]);
            } else {
                throw new ParseException(null, 'PARENT_NOT_FOUND', [$context->curLine()->getLine()]);
            }
            
            //if current source closure has nested closures then we can guarantee that the next line is deeper than the current one
            if ($context->curClosure()->countChildren() > 0 && $result instanceof NodeBlock) {
                $curBlock = $result;
            }
        } while ($curLine = $context->nextLine(true));
        
        return $rootBlock->getChildren();
    }
    
    protected function tryParse(ParserContext $context, ParserExtension $extension) {
        $result = $context->parse($extension);
        if (!$result) {
            return false;
        }
        return $result;
    }

    protected function getParentClosureForLevel(SourceClosure $block, $level) {
        if ($block->getLevel() >= $level) {
            while ($level <= $block->getLevel()) {
                $block = $block->getParent();
            }
        }
        return $block;
    }
    
    protected function addChildToClosure(SourceClosure $block) {
        return $block->addChildClosure(new SourceClosure())
                     ->getChildClosure(-1);
    }
    
    protected function findAppropriateClosure(SourceClosure $curBlock, $neededLevel) {
        $curBlockLevel = $curBlock->getLevel();
//        var_dump($neededLevel . ' ' . $curBlockLevel);
        if ($neededLevel === $curBlockLevel && !$curBlock->hasChildren()) {
            return $curBlock;
        }
        //if level of current line is less than the level of current block, we need to find appropriate parent for it
        $curBlockNew = $this->getParentClosureForLevel($curBlock, $neededLevel);
        //we've gotta add a new block if the levels of current line and previous block are not the same
        if ($curBlockLevel !== $neededLevel || $curBlock->hasChildren()) {
            $curBlockNew = $this->addChildToClosure($curBlockNew);
        }
        return $curBlockNew;
    }
    
    protected static function countLineSpaces($line, $tabsInsteadSpaces = false, &$indentSize = null) {
        $spaces_count = 0;
        if ($indentSize === null) {
            $spaces_count = StringHelper::countLineSpaces($line, $tabsInsteadSpaces);
            if ($spaces_count > 0) {
                $indentSize = $spaces_count;
            }
        } else {
            $spaces_count = StringHelper::countLineSpaces($line, $tabsInsteadSpaces);
        }
        return $spaces_count;
    }
}
