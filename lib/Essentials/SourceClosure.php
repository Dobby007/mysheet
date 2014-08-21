<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Essentials;

/**
 * Description of SourceBlock
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class SourceClosure {
    private $parent;
    private $lines = array();
    private $children = array();
    
//    public function __construct($parentBlock = null) {
//        $this->setParent($parentBlock);
//    }

    /**
     * Function returns parent closure
     * @return SourceClosure|null
     */
    public function getParent() {
        return $this->parent;
    }

    protected function setParent(SourceClosure $parent) {
        $this->parent = $parent;
        return $this;
    }
    
    /**
     * Function returns the index of the specified closure or false if it's not found.
     * @param SourceClosure $closure
     * @return int|boolean
     */
    public function indexOf(SourceClosure $closure) {
        foreach ($this->children as $index => $child) {
            if ($child === $closure) {
                return $index;
            }
        }
        return false;
    }
    
    public function getPrevNeighbour() {
        return $this->getNeighbour(false);
    }
    
    public function getNextNeighbour() {
        return $this->getNeighbour(true);
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
            if ($index < $parent->countChildren()) {
                return $parent->getChildClosure($index);
            } else {
                return $parent->getNeighbour($getNext, true);
            }
        }
//        echo $neighbour;
        return $neighbour;
    }
    
    public function getLevel() {
        $block = $this;
        $level = 0;
        while ($block->getParent() instanceof SourceClosure) {
            $block = $block->getParent();
            $level++;
        }
        return $level;
    }
    
    public function getLine($index) {
        if (isset($this->lines[$index])) {
            return new SourceLine($this->lines[$index], $this->getLevel());
        }
        return false;
    }
    
    public function setLine($index, $line) {
        if (($line instanceof SourceLine)) {
            $line = new SourceLine($line);
        }
        if ($index !== null && isset($this->lines[$index])) {
            $this->lines[$index] = (string) $line;
        } else if ($index === null) {
            $this->lines[] = (string) $line;
        }
        return $this;
    }
    
    public function addLine($line) {
        return $this->setLine(null, $line);
    }
    
    public function countLines() {
        return count($this->lines);
    }
    
    public function getLines() {
        return $this->lines;
    }
    
    
    public function getChildClosure($index) {
        if ($index < 0) {
            $index = count($this->children) + $index;
        }
        
        if (isset($this->children[$index])) {
            return $this->children[$index];
        }
        return null;
    }
    
    public function setChildClosure($index, SourceClosure $block) {
        if ($index !== null && isset($this->children[$index])) {
            $this->children[$index] = $block;
            $block->setParent($this);
        } else if ($index === null) {
            $this->children[] = $block;
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
        return $this->children;
    }
    
    public function countChildren() {
        return count($this->children);
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
        foreach ($this->children as $child) {
            $result .= (string)$child;
        }
        return $result;
    }
    
    public function __toString() {
        return $this->convertToString();
    }

}
