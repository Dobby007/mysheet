<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Essentials;

use MySheet\Tools\IParser;
use MySheet\Traits\ParserLinesTrait;
use MySheet\Traits\ParserCursorStateTrait;
use MySheet\Essentials\ParserExtension;
use MySheet\Essentials\SourceClosure;
/**
 * Description of ParserContext
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class ParserContext {
    use ParserLinesTrait, ParserCursorStateTrait;
    
    /**
     * @var IParser 
     */
    private $parser;
    
    /**
     * @var int
     */
    private $lineIndex;
    
    /**
     * @var SourceClosure
     */
    private $sourceClosure;
    
    /**
     * @var SourceClosure
     */
    private $rootSourceClosure;
    
    public function __construct(IParser $parser, SourceClosure $rootSourceClosure) {
        $this->rootSourceClosure = $rootSourceClosure;
        $this->setParser($parser);
        $this->setCursor($this->rootSourceClosure->getChildClosure(0), 0);
    }
 
    /**
     * 
     * @return IParser
     */
    public function getParser() {
        return $this->parser;
    }
    
    public function setParser(IParser $parser) {
        $this->parser = $parser;
    }
    
    public function nextLine($jump = false) {
        if ($this->getLine($this->getCurrentLineIndex() + 1)) {
            $this->setCurrentLineIndex($this->getCurrentLineIndex() + 1);
            return $this->curLine();
        } else if ($jump === true) {
            do {
                if (!$this->goToClosure(1)) {
                    return false;
                }
            } while (!$this->getLine(0));
            
            $this->setCurrentLineIndex(0);
            return $this->curLine();
        }
        return false;
    }
    
    public function prevLine($jump = false) {
        if ($this->getLine($this->getCurrentLineIndex() - 1)) {
            $this->setCurrentLineIndex($this->getCurrentLineIndex() - 1);
            return $this->curLine();
        } else if ($jump === true) {
            do {
                if (!$this->goToClosure(-1)) {
                    return false;
                }
            } while (!$this->getLine(0));
//            echo($this->curClosure());
            $this->setCurrentLineIndex(-1);
            return $this->curLine();
        }
        return false;
    }
    
    public function curLine() {
        if ($this->curClosure()) {
            return $this->curClosure()->getLine($this->lineIndex);
        }
        return false;
    }   
    
    /**
     * Function is used to return the source block where the cursor is set
     * @return SourceClosure
     */
    public function curClosure() {
        return $this->sourceClosure;
    }
    
    public function goToClosure($offset) {
        $myClosure = $this->curClosure();
        while ($offset !== 0) {
            if ($offset > 0) {
                $myClosure = $myClosure->getNextNeighbour();
                $offset--;
            } else {
                $myClosure = $myClosure->getPrevNeighbour();
                $offset++;
            }
        }
        if ($myClosure instanceof SourceClosure) {
            $this->setCursor($myClosure, 0);
            return $myClosure;
        }
        return false;
    }
    
    public function getCurrentLineIndex() {
        return $this->lineIndex;
    }
    
    public function setCursor(SourceClosure $closure, $lineIndex) {
        $this->sourceClosure = $closure;
        $this->setCurrentLineIndex($lineIndex);
    }
    
    public function setCurrentLineIndex($lineIndex) {
        if (!$this->curClosure()) {
            return false;
        }
        
        if ($lineIndex < 0) {
            $lineIndex = $this->curClosure()->countLines() + $lineIndex;
        }
        
        if ($lineIndex >= 0 && $lineIndex < $this->curClosure()->countLines()) {
            $this->lineIndex = $lineIndex;
        }
        
        return false;
    }
    
    public function getLine($lineIndex) {
        if (!$this->curClosure()) {
            return false;
        }
        
        return $this->curClosure()->getLine($lineIndex);
    }
    
    
    
    /**
     * Method runs parsing for this parser context
     * @param ParserExtension $extension The extension you want to parse context with
     * @return \MySheet\Structure\Block|bool
     */
    public function parse(ParserExtension $extension) {
        $extension->setContext($this);
        return $extension->parse();
    }
    
    private $savedCursor = null;
    
    public function saveCursorState() {
        $this->savedCursor = [$this->curClosure(), $this->getCurrentLineIndex()];
    }

    public function restoreCursorState() {
        if ($this->savedCursor !== null) {
            $this->setCursor($this->savedCursor[0], $this->savedCursor[1]);
            return true;
        }
        return false;
    }
}
