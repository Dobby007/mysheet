<?php

namespace MySheet\Tools;

use MySheet\Tools\IParser;
use MySheet\Structure\NodeBlock;
use MySheet\Structure\Document;
use MySheet\Error\ParseException;
use MySheet\Error\ErrorTable;
use MySheet\Essentials\ParserExtension;
use MySheet\Essentials\ParserContext;
use MySheet\Essentials\SourceLine;
use MySheet\Essentials\SourceClosure;
use MySheet\Traits\RootClassTrait;
use MySheet\Traits\ParserLinesTrait;
use MySheet\Traits\ParserCursorStateTrait;
use MySheet\Helpers\StringHelper;

/**
 * Description of Parser
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class BlockParser implements IParser {

    use RootClassTrait,
        ParserLinesTrait,
        ParserCursorStateTrait;

    protected $extensions = array();
    
    private $code = null;
    private $sourceClosure = null;
    private $doc = null;

    public function __construct($code) {
        $this->setCode($code);
    }

    public function getCode() {
        return $this->code;
    }

    public function setCode($code) {
        $this->code = (string) $code;
        $this->resetCursor();
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

        try {
            $this->divideIntoLines();
            $context = new ParserContext($this, $this->sourceClosure);
            $parsedBlocks = $this->parseContext($context);
            foreach ($parsedBlocks as $parsedBlock) {
                $this->doc->addChild($parsedBlock);
            }
        } catch (ParseException $exc) {
            echo 'Error happened: ' . (string)$exc . "\n";
        }
        return $this->doc;
    }

    protected function divideIntoLines() {
        $this->lines = preg_split("/\n|\r\n/", $this->code);

        $indentSize = null;
        $tabsInsteadSpaces = false;
        /* @var $lines SourceLine[] */
        $lines = [];
        $curly_blocks = [];
        $blockTree = [];
        
        $rootBlock = (new SourceClosure())->addChildClosure(new SourceClosure());
        /* @var $curBlock SourceClosure */
        $curBlock = $rootBlock->getChildClosure(-1);
        
        array_walk($this->lines, function($line, $index) use(&$indentSize, &$tabsInsteadSpaces, &$lines, &$curly_blocks, &$curBlock, &$blockTree) {
            /* @var $curBlock SourceClosure */
            if ($indentSize === null && substr($line, 0, 1) === "\t") {
                $tabsInsteadSpaces = true;
            }
            $spacesCount = self::countLineSpaces($line, $tabsInsteadSpaces, $$indentSize);
            if ($spacesCount === false) {
                return;
            }
            
            $curBlockLevel = $curBlock->getLevel();
            $lineLevel = $$indentSize > 0 ? intval($spacesCount / $$indentSize) : 0;
            $lineLevel++;
            if (count($curly_blocks) > 0 && $lineLevel <= end($curly_blocks)) {
                if ($lineLevel > $curBlockLevel + 1) {
                    $lineLevel = $curBlockLevel + 1;
                } else {
                    $lineLevel = end($curly_blocks);
                }
            }
            
            $newLevel = $lineLevel;
            $len = strlen($line);
            $offset = $spacesCount;
            $i = $offset;
            $handled = false;
            
            while ($i < $len) {
                if ($line[$i] === '{') {
                    $newLine = trim(substr($line, $offset, $i - $offset));
                    if (!empty($newLine)) {
                        if (!$handled) {
                            $curBlock = $this->findAppropriateClosure($curBlock, $lineLevel - 1);
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
                    if (!empty($newLine)) {
                        $curBlock->addLine($newLine);
                    }
                    array_pop($curly_blocks);
                    $newLevel--;
                    $curBlock = $this->getParentClosureByLevel($curBlock, $newLevel);
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
                $curBlock = $this->findAppropriateClosure($curBlock, $lineLevel);
                $curBlock->addLine(rtrim(substr($line, $spacesCount)));
            }
        });
        
        echo "\n\n-==LINES==-\n";
        while ($curBlock->getParent()) {
            $curBlock = $curBlock->getParent();
        }
        echo $curBlock;
        echo "\n===\n\n";
        var_dump($curBlock);
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
            foreach ($this->extensions as $extension) {
                $result = $this->tryParse($context, $extension);
                if ($result !== false) {
                    break;
                }
                $context->restoreCursorState();
            }
            
            var_dump('level is ' . $curBlock->getDepth(), 'source level is ' . $curLine->getLevel());
            
            if (
                $curBlock instanceof NodeBlock && 
                $result !== null
            ) {
                $curBlock->addChild($result);
            } else if (!($result instanceof \MySheet\Structure\Block)) {
                //throw unrecognized sequence
                throw new ParseException(ErrorTable::E_UNRECOGNIZED_SEQUENCE);
            } else {
                //throw can not get parent object of $curLineNumber
            }
            
            //if current source closure has nested closures then we can guarantee that the next line is deeper than the current one
            if ($context->curClosure()->countChildren() > 0) {
                $curBlock = $result;
            }
            
            /*
            $nextLine = $this->getLine($this->getLineNumber() + 1);
            if ($result && $nextLine) {
                if (
                    $result instanceof NodeBlock && 
                    $nextLine->getLevel() >= $this->curline()->getLevel()
                ) {
                    $curBlock = $result;
                } else if ($nextLine->getLevel() < $curLine->getLevel()) {
                    $steps_back = $curLine->getLevel() - $nextLine->getLevel();
                    while ($steps_back --) {
                        $curBlock = $curBlock->getParent();
                        if ($curBlock === null) {
                            //throw can not get parent object of $this->getLineNumber() + 1
                        }
                    }
                } else {
                    //throw bad tab indentation
                }
            }*/
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

    protected function getParentClosureByLevel(SourceClosure $block, $level) {
        if ($level < $block->getLevel()) {
            while ($level < $block->getLevel()) {
                $block = $block->getParent();
            }
        }
        return $block;
    }
    
    protected function addChildToClosure(SourceClosure $block) {
        return $block->addChildClosure(new SourceClosure())
                     ->getChildClosure(-1);
    }
    
    protected function findAppropriateClosure(SourceClosure $curBlock, $lineLevel) {
        $curBlockLevel = $curBlock->getLevel();
        //if level of current line is less than the level of current block, we need to find appropriate parent for it
        $curBlock = $this->getParentClosureByLevel($curBlock, $lineLevel);
        //we've gotta add a new block if the levels of current line and previous block are not the same
        if ($lineLevel !== $curBlockLevel) {
            $curBlock = $this->addChildToClosure($curBlock);
        }
        return $curBlock;
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
