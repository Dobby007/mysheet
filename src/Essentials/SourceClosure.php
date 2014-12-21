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

/**
 * Description of SourceBlock
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class SourceClosure {
    private $_parent;
    private $_lines = array();
    private $_children = array();
    
    protected $_cachedLevel;
    
//    public function __construct($parentBlock = null) {
//        $this->setParent($parentBlock);
//    }

    /**
     * Function returns parent closure
     * @return SourceClosure|null
     */
    public function getParent() {
        return $this->_parent;
    }

    protected function setParent(SourceClosure $parent) {
        $this->_parent = $parent;
        $this->updateLevel();
        return $this;
    }
    
    /**
     * Function returns the index of the specified closure or false if it's not found.
     * @param SourceClosure $closure
     * @return int|boolean
     */
    public function indexOf(SourceClosure $closure) {
        foreach ($this->_children as $index => $child) {
            if ($child === $closure) {
                return $index;
            }
        }
        return false;
    }
    
    public function getPrevNeighbour() {
        $parent = $this->getParent();
        if ($parent === null) {
            return null;
        }
        $index = $parent->indexOf($this) - 1;
        if ($index >= 0 && $index < $parent->countChildren()) {
            $closure = $parent->getChildClosure($index);
            while ($closure->hasChildren()) {
                $closure = $closure->getChildClosure(-1);
            }
            return $closure;
        } else {
            return $parent;
        }
    }
    
    public function getNextNeighbour() {
        return $this->getNeighbour(true);
    }
    
    protected function getDeepestElement() {
        
    }
    
    protected function getNeighbour($getNext, $ignoreChildren = false) {
        $neighbour = null;
        if (!$ignoreChildren && ($msb = $this->getChildClosure($getNext ? 0 : -1))) {
            $neighbour = $msb;
        } else {
            $parent = $this->getParent();
            if ($parent === null) {
                return null;
            }
            $index = $parent->indexOf($this) + ($getNext ? 1 : -1);
            if ($index >= 0 && $index < $parent->countChildren()) {
                return $parent->getChildClosure($index);
            } else {
                return $parent->getNeighbour($getNext, true);
            }
        }
        return $neighbour;
    }
    
    protected function updateLevel() {
        $block = $this;
        $level = 0;
        while ($block->getParent() instanceof SourceClosure) {
            $block = $block->getParent();
            $level++;
        }
        $this->_cachedLevel = $level;
        return $this;
    }
    
    public function getLevel() {
        return $this->_cachedLevel;
    }
    
    public function getLine($index) {
        if (isset($this->_lines[$index])) {
            return $this->_lines[$index];
        }
        return false;
    }
    
    public function setLine($index, $line) {
        // TODO: WTF????
        if (!($line instanceof SourceLine)) {
            $line = new SourceLine((string)$line, $this);
        }
        if ($index !== null && isset($this->_lines[$index])) {
            $this->_lines[$index] = $line;
        } else if ($index === null) {
            $this->_lines[] = $line;
        }
        return $this;
    }
    
    public function addLine($line) {
        return $this->setLine(null, $line);
    }
    
    public function countLines() {
        return count($this->_lines);
    }
    
    public function getLines() {
        return $this->_lines;
    }
    
    
    public function getChildClosure($index) {
        if ($index < 0) {
            $index = $this->countChildren() + $index;
        }
        
        if (isset($this->_children[$index])) {
            return $this->_children[$index];
        }
        return null;
    }
    
    public function setChildClosure($index, SourceClosure $block) {
        if ($index !== null && isset($this->_children[$index])) {
            $this->_children[$index] = $block;
            $block->setParent($this);
        } else if ($index === null) {
            $this->_children[] = $block;
            $block->setParent($this);
        }
        return $this;
    }
    
    public function addChildClosure(SourceClosure $block) {
        return $this->setChildClosure(null, $block);
    }
    
    /**
     * Get all children that belong to this closure
     * @return SourceClosure[]
     */
    public function getChildren() {
        return $this->_children;
    }
    
    public function countChildren() {
        return count($this->_children);
    }
    
    public function hasChildren() {
        return $this->countChildren() > 0;
    }

    public function convertToString($indentStr = '    ') {
        $result = '';
        $level = $this->getLevel();
        foreach ($this->getLines() as $line) {
            $result .= str_repeat($indentStr, $level) . $line . "\n";
        }
        foreach ($this->_children as $child) {
            $result .= (string)$child;
        }
        return $result;
    }
    
    public function __toString() {
        return $this->convertToString();
    }

}
