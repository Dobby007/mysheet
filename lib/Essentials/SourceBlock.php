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
class SourceBlock {
    private $parent;
    private $level;
    private $lines = array();
    private $children = array();
    
//    public function __construct($parentBlock = null) {
//        $this->setParent($parentBlock);
//    }

    public function getParent() {
        return $this->parent;
    }

    protected function setParent(SourceBlock $parent) {
        $this->parent = $parent;
        return $this;
    }
    
    public function getLevel() {
        $block = $this;
        $level = 0;
        while ($block->getParent() instanceof SourceBlock) {
            $block = $block->getParent();
            $level++;
        }
        return $level;
    }
    
    public function getLine($index) {
        if (isset($this->lines[$index])) {
            return $this->lines[$index];
        }
        return null;
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
    
    
    public function getChildBlock($index) {
        if ($index < 0) {
            $index = count($this->children) + $index;
        }
        
        if (isset($this->children[$index])) {
            return $this->children[$index];
        }
        return null;
    }
    
    public function setChildBlock($index, SourceBlock $block) {
        if ($index !== null && isset($this->children[$index])) {
            $this->children[$index] = $block;
            $block->setParent($this);
        } else if ($index === null) {
            $this->children[] = $block;
            $block->setParent($this);
        }
        return $this;
    }
    
    public function addChildBlock(SourceBlock $block) {
        return $this->setChildBlock(null, $block);
    }
    
    /**
     * Get all children that belong to this block
     * @return SourceBlock[]
     */
    public function getChildren() {
        return $this->children;
    }
    
    public function countChildren() {
        return count($this->children);
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
