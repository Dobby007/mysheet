<?php

namespace MySheet\Tools;

use MySheet\Tools\IParser;
use MySheet\Structure\NodeBlock;
use MySheet\Structure\Document;
use MySheet\Error\ParseException;
use MySheet\Error\ErrorTable;
use MySheet\Essentials\ParserExtension;
use MySheet\Essentials\ParserContext;
use MySheet\Traits\RootClassTrait;
use MySheet\Traits\ParserLinesTrait;
use MySheet\Traits\ParserCursorStateTrait;

/**
 * Description of Parser
 *
 * @author dobby007
 */
class Parser implements IParser {

    use RootClassTrait,
        ParserLinesTrait,
        ParserCursorStateTrait;

    private $code = null;
    private $curBlock = null;
    private $doc = null;
    protected $extensions = array();

    public function __construct($code, $rootInstance) {
        $this->setRoot($rootInstance);
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
            $extension->setRoot($this->getRoot());
            $this->extensions[] = $extension;
        }
    }

    public function comeon() {
        $this->doc = new Document();
        $this->doc->setRoot($this->getRoot());
        $this->curBlock = $this->doc;

        try {
            $this->divideIntoLines();
            $this->linebyline();
        } catch (ParseException $exc) {
            echo 'Error happened: ' . $exc->getErrorCode() . ' in file ' . $exc->getFile() . ':' . $exc->getLine() . "\n" . implode($exc->getArguments(), ', ') . "\n";
        }
//        var_dump($this->lines);
        return $this->doc;
    }

    protected function divideIntoLines() {
        $this->lines = preg_split("/\n|\r\n/", $this->code);

        $tabsize = false;
        $class = $this;
        $is_tabbed = false;
        $lines = [];
        array_walk($this->lines, function($line, $index) use(&$tabsize, &$is_tabbed, &$lines, $class) {
            $spaces_count = 0;
            if ($tabsize === false) {
                if (substr($line, 0, 1) === "\t")
                    $is_tabbed = true;

                $spaces_count = $class->linespaces($line, $is_tabbed);
                if ($spaces_count > 0) {
                    $tabsize = $spaces_count;
                }
            } else {
                $spaces_count = $class->linespaces($line, $is_tabbed);
            }

            if ($spaces_count === false) {
                return;
            }
            $level = $tabsize > 0 ? intval($spaces_count / $tabsize) : 0;

            $lines[] = [$level, rtrim(substr($line, $spaces_count))];
        });

        $this->lines = $lines;
    }

    protected function linebyline() {
        $this->curLine = 0;
        $curLine = $this->curline();

        do {
            $curLineNumber = $this->getLineNumber();
            $result = false;

            foreach ($this->extensions as $extension) {
                $result = $this->tryParse($extension);
                if ($result !== false)
                    break;
            }
            
//            var_dump('new result:', $curLine, $this->curBlock, $result);
//            var_dump($this->curBlock, $curLine, $result);
            if (
                $this->curBlock instanceof NodeBlock && 
                $result !== null
            ) {
                $this->curBlock->addChild($result);
            } else if ($result === false) {
                //throw unrecognized sequence
                throw new ParseException(ErrorTable::E_UNRECOGNIZED_SEQUENCE);
            } else {
                //throw can not get parent object of $curLineNumber
            }

//            var_dump($curLine,$result ? $result->getSelectors()[0]->getPath() : null);

            $nextLine = $this->getLine($this->getLineNumber() + 1);
//            var_dump('status: ', $curLine, $this->curline(), $nextLine);
            if ($result && $nextLine) {
                if (
                    $result instanceof NodeBlock && 
                    $nextLine[0] == $this->curline()[0]
                ) {
                    $this->curBlock = $result;
//                    var_dump('indent + 1 : ', $result->getSelectors());
                } else if ($nextLine[0] < $curLine[0]) {
                    $steps_back = $curLine[0] - $nextLine[0];
                    while ($steps_back --) {
                        $this->curBlock = $this->curBlock->getParent();
                        if ($this->curBlock === null) {
                            //throw can not get parent object of $this->getLineNumber() + 1
                        }
                    }
                } else {
                    //throw bad tab indentation
                }
            }
        } while ($curLine = $this->nextline());
    }

    protected function tryParse(ParserExtension $extension) {
        $result = false;
        $context = new ParserContext($this, $this->lines, $this->getLineNumber());

        $result = $context->parse($extension);
        if (!$result) {
            return false;
        }
        $this->applyParserContext($context);

        return $result;
    }

    protected function applyParserContext(ParserContext $context) {
        $this->setLineCursor($context->getLineNumber());
    }

    /**
     * 
     * @param string $line Text line
     * @param boolean $count_tabs True if you want to count \t characters instead of spaces
     * @return int|boolean Returns the number of spaces till the first meaning character
     */
    protected function linespaces($line, $count_tabs = false) {
        $linelen = strlen($line);
        $i = 0;

        while ($i < $linelen) {
            if (!$this->is_space_symbol($line[$i]) && $i > 0)
                return $i;
            else if ($line[$i] === "\r" || $line[$i] === "\n")
                return false;
            else if ($i === 0 && !$this->is_space_symbol($line[$i])) {
                return 0;
            }

            $i++;
        }

        return false;
    }

    protected function is_space_symbol($char) {
        if ($char === ' ' || $char === "\t")
            return true;

        return false;
    }

}
