<?php

/*
 *  Copyright 2014 Alexander Gilevich (alegil91@gmail.com)
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at 
 * 
 *      http://www.apache.org/licenses/LICENSE-2.0
 */

namespace MSSLib\Essentials;

use MSSLib\Essentials\IParser;
use MSSLib\Essentials\ParserExtension;
use MSSLib\Essentials\SourceClosure;
use MSSLib\Essentials\ParserContextMemento;

/**
 * Description of ParserContext
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class ParserContext {
    /**
     * @var IParser 
     */
    private $_parser;
    
    /**
     * @var int
     */
    private $_lineIndex;
    
    /**
     * @var SourceClosure
     */
    private $_sourceClosure;
    
    /**
     * @var SourceClosure
     */
    private $_upperClosureLimit;
    
    /**
     * @var SourceClosure
     */
    private $_rootSourceClosure;
    
    private $_savedCursor = null;
    
    
    public function __construct(IParser $parser, SourceClosure $rootSourceClosure) {
        $this->_rootSourceClosure = $rootSourceClosure;
        $this->setParser($parser);
        $this->setCursor($this->_rootSourceClosure->getChildClosure(0), 0);
    }
 
    /**
     * 
     * @return IParser
     */
    public function getParser() {
        return $this->_parser;
    }
    
    public function setParser(IParser $parser) {
        $this->_parser = $parser;
    }
    
    public function nextLine($jumpIn = false, $jumpOut = false) {
        if ($this->getLine($this->getCurrentLineIndex() + 1)) {
            $this->setCurrentLineIndex($this->getCurrentLineIndex() + 1);
            return $this->curLine();
        } else if ($jumpIn === true) {
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
            $this->setCurrentLineIndex(-1);
            return $this->curLine();
        }
        return false;
    }
    
    /**
     * 
     * @return SourceLine|false
     */
    public function curLine() {
        if ($this->curClosure()) {
            return $this->curClosure()->getLine($this->_lineIndex);
        }
        return false;
    }   
    
    /**
     * Function is used to return the source block where the cursor is set
     * @return SourceClosure
     */
    public function curClosure() {
        return $this->_sourceClosure;
    }
    
    public function goToClosure($offset) {
        $myClosure = $this->curClosure();
        while ($offset !== 0) {
            if ($offset > 0) {
                $myClosure = $myClosure->getNextNeighbour($this->getUpperClosureLimit());
                $offset--;
            } else {
                $myClosure = $myClosure->getPrevNeighbour($this->getUpperClosureLimit());
                $offset++;
            }
        }
        if ($myClosure instanceof SourceClosure) {
            $this->setCursor($myClosure, 0);
            return $myClosure;
        }
        return false;
    }
    
    public function getGlobalLineNumber() {
        $line = $this->getCurrentLineIndex();
        while ($myClosure = $this->curClosure()->getPrevNeighbour()) {
            $line += $myClosure->countLines();
        }
        return $line;
    }
    
    public function getCurrentLineIndex() {
        return $this->_lineIndex;
    }
    
    public function setCursor(SourceClosure $closure, $lineIndex) {
        $this->_sourceClosure = $closure;
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
            $this->_lineIndex = $lineIndex;
        }
        
        return false;
    }
    
    public function getUpperClosureLimit() {
        return $this->_upperClosureLimit;
    }

    public function setUpperClosureLimit(SourceClosure $upperClosureLimit = null) {
        $this->_upperClosureLimit = $upperClosureLimit;
        return $this;
    }
    
    public function setCurrentClosureAsUpperLimit() {
        return $this->setUpperClosureLimit($this->curClosure());
    }
        
    /**
     * 
     * @param int $lineIndex
     * @return false|SourceLine
     */
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
    
    /**
     * Saves state into new Memento object and returns it
     * @return ParserContextMemento
     */
    public function createMemento() {
        $memento = new ParserContextMemento();
        $memento->closure = $this->curClosure();
        $memento->lineIndex = $this->getCurrentLineIndex();
        $memento->upperClosureLimit = $this->getUpperClosureLimit();
        return $memento;
    }
    
    /**
     * Restores state from Memento object
     * @param ParserContextMemento $memento
     */
    public function setMemento(ParserContextMemento $memento) {
        $memento->closure = $memento->closure;
        $memento->lineIndex = $memento->lineIndex;
        $memento->upperClosureLimit = $memento->upperClosureLimit;
    }
}
