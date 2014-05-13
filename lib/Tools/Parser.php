<?php

namespace MySheet\Tools;


use MySheet\Tools\IParser;
use MySheet\Structure\Block;
use MySheet\Structure\Document;
use MySheet\Structure\Ruleset;
use MySheet\Structure\Selector;
use MySheet\Structure\Declaration;
use MySheet\Error\ParseException;
use MySheet\Error\ErrorTable;

/**
 * Description of Parser
 *
 * @author dobby007
 */
class Parser implements IParser {
    use \MySheet\Traits\RootClassTrait;
    
    private $code = null;
    private $lines;
    private $curLine = false;
    private $curBlock = null;
    private $doc = null;
    private $savedCursor = null;
    
    public function __construct($code, $rootInstance) {
        $this->setRoot($rootInstance);
        $this->setCode($code);
    }

    public function getCode() {
        return $this->code;
    }

    public function setCode($code) {
        $this->code = (string) $code;
    }

    public function comeon() 
    {
        $this->doc = new Document();
        $this->curBlock = $this->doc;
        
        try
        {
            $this->divideIntoLines();
            $this->linebyline();
        } catch (ParseException $exc) {
            echo 'Error happened: ' . $exc->getErrorCode() . ' in file ' . $exc->getFile() . ':' . $exc->getLine() . "\n";
        }
//        var_dump($this->lines);
        return $this->doc;
    }

    protected function divideIntoLines() {
        $this->lines = split("\n|\r\n", $this->code);
        
        
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

    protected function linebyline() {
        $this->curLine = 0;
        $curLine = $this->curline();

        do {
            $curLineNumber = $this->getLineNumber();
            $result = $this->tryParse('mixin');
            
            if (!$result) {
                $result = $this->tryParse('ruleset');
            }
//            var_dump('new result:', $curLine, $this->curBlock, $result);
//            var_dump($this->curBlock, $curLine, $result);
            if (!$result) {
                //throw unrecognized sequence
                throw new ParseException(ErrorTable::E_UNRECOGNIZED_SEQUENCE);
            } else if ($this->curBlock instanceof Block) {
                $this->curBlock->addChild($result);
            } else {
                //throw can not get parent object of $curLineNumber
            }
            
            var_dump($curLine,$result ? $result->getSelectors()[0]->getPath() : null);

            $nextLine = $this->getLine($this->getLineNumber() + 1);
//            var_dump('status: ', $curLine, $this->curline(), $nextLine);
            if ($result && $nextLine) {
                if ($nextLine[0] == $this->curline()[0]) {
                    $this->curBlock = $result;
//                    var_dump('indent + 1 : ', $result->getSelectors());
                } else if ($nextLine[0] < $curLine[0]) {
                    $steps_back = $curLine[0] - $nextLine[0];
                    while ($steps_back--) {
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

    protected function prevline() {
        if ($this->curLine > 0) {
            $this->curLine --;
            return $this->curline();
        }
        return false;
    }

    protected function nextline() {
        if ($this->curLine + 1 < count($this->lines)) {
            $this->curLine ++;
            return $this->curline();
        }
        return false;
    }

    protected function curline() {
        if ($this->curLine === false)
            return false;

        return $this->lines[$this->curLine];
    }

    protected function getLine($line_number) {
        if ($line_number >= count($this->lines))
            return false;

        return $this->lines[$line_number];
    }

    protected function getLineNumber() {
        return $this->curLine;
    }

    protected function setLineCursor($nn) {
        $this->curLine = intval($nn);
    }

    protected function saveCursorState() {
        $this->savedCursor = $this->getLineNumber();
    }

    protected function restoreCursorState() {
        if ($this->savedCursor !== null) {
            $this->setLineCursor($this->savedCursor);
            return true;
        }
        return false;
    }

    protected function tryParse($blockType) {
        $result = false;
        $method = 'parse' . ucfirst($blockType);
        $this->saveCursorState();

        try {

            if (
                    !method_exists($this, $method) || 
                    !($result = $this->$method())
            ) {
                throw new ParseException(ErrorTable::E_UNRECOGNIZED_SEQUENCE);
            }
        } catch (\Exception $ex) {
            $this->restoreCursorState();
        }
        return $result;
    }
    
    protected function parseMixin() {
        $firstLine = $curLine = $this->curline();
        
        if (substr($firstLine[1], 0, 6) !== '@mixin')
            return false;
        
        if ($firstLine[0] !== 0)
            throw new ParseException(ErrorTable::E_BAD_INDENTATION);
        
        
        do {
            
        } while ($curLine = $this->nextline());
    }
    
    protected function parseRuleset() {
        $firstLine = $curLine = $this->curline();
        $ruleset = new Ruleset($this->curBlock);
        $ruleset->setRoot($this->getRoot());
        do {
            if ($curLine[0] == $firstLine[0]) {
//                var_dump('selector:', $curLine);
                $selector = split(',', $curLine[1]);
                $ruleset->addSelectors($selector);
            } else if ($curLine[0] == $firstLine[0] + 1) {
                $nextline = $this->getLine($this->getLineNumber() + 1);
                $declaration = $curLine[1];

                if ($nextline) {
                    if ($firstLine[0] >= $nextline[0]) {
                        $ruleset->addDeclaration($declaration);
                        return $ruleset;
                    } else if ($nextline[0] > $curLine[0]) {
                        $this->prevline();
                        return $ruleset;
                    } else if (!Declaration::canBeDeclaration($nextline[1])) {
                        $ruleset->addDeclaration($declaration);
                        return $ruleset;
                    }
                }

                $ruleset->addDeclaration($declaration);
            } else {
                return false;
            }
        } while ($curLine = $this->nextline());

        if ($ruleset->countDeclarations() > 0) {
            return $ruleset;
        }


        return false;
    }

//    
//    protected function checkSelector() {
//        
//    }
//    
//    protected function checkDeclaration() {
//        
//    }

    protected function is_space_symbol($char) {
        if ($char === ' ' || $char === "\t")
            return true;

        return false;
    }

}
