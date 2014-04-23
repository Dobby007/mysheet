<?php

namespace MySheet\Tools;

require_once 'IParser' . EXT;
require_once ROOTDIR . 'structure/Document' . EXT;
require_once ROOTDIR . 'structure/Ruleset' . EXT;

use MySheet\Structure\Document;
use MySheet\Structure\Ruleset;
use MySheet\Structure\Selector;
use MySheet\Structure\Declaration;

/**
 * Description of Parser
 *
 * @author dobby007
 */
class Parser implements IParser {

    private $code = null;
    private $lines;
    private $curLine = false;
    private $curBlock = null;
    
    private $doc = null;

    public function __construct($code) {
        $this->setCode($code);
        
    }

    public function getCode() {
        return $this->code;
    }

    public function setCode($code) {
        if (is_string($code))
            $this->code = $code;
    }

    public function comeon() {
        $this->doc = new Document();
        $this->curBlock = $this->doc;
        
        $this->divideIntoLines();
        $this->linebyline();
        var_dump($this->lines);
        var_dump($this->doc);
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
                if ($line[0] === "\t")
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
            $result = false;
            
            if ($result = $this->parseRuleset()) {
                $this->curBlock->addChild($result);
                
            }
            
            if (!$result) {
                //throw unrecognized sequence
            }
            
            var_dump($result ? $result->getSelectors() : null);
            
            $nextLine = $this->getLine($this->getLineNumber() + 1);
            var_dump('status: ', $curLine, $this->curline(), $nextLine);
            if ($nextLine) {
                if ($nextLine[0] == $this->curline()[0]) {
                    $this->curBlock = $result;
//                    var_dump('indent + 1 : ', $result->getSelectors());
                } else if ($nextLine[0] < $curLine[0] ) {
                    //todo: get real parent
                    $this->curBlock = $this->curBlock->getParent();
                } else {
                    //throw bad tab indentation
                }
            }
        } 
        while ($curLine = $this->nextline());
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
    
    protected function tryParse($blockType) {
        //savestate
        
        ///parse
        
        //restore in cae of bad try
    }
    
    protected function parseRuleset() {
        $firstLine = $curLine = $this->curline();
        $ruleset = new Ruleset($this->curBlock);
        do {
            if ($curLine[0] == $firstLine[0]) {
                $selector = new Selector($curLine[1]);
                $ruleset->addSelector($selector);
            } else if ($curLine[0] == $firstLine[0] + 1) {
                
                $nextline = $this->getLine($this->getLineNumber() + 1);
                if ($nextline) {
                    if ($firstLine[0] == $nextline[0]) {
                        return $ruleset;
                    } else if ($nextline[0] > $curLine[0]) {
                        $this->prevline();
                        return $ruleset;
                    }
                }
                
                $declaration = new Declaration($curLine[1]);
                $ruleset->addDeclaration($declaration);
            } else {
                return false;
            }
        } 
        while ($curLine = $this->nextline());
        
        if ($ruleset->countDeclarations() > 0)
            return $ruleset;
        
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
