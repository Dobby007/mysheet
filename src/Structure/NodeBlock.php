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

namespace MSSLib\Structure;

use MSSLib\Structure\Block;
use MSSLib\Essentials\VariableScope;
use MSSLib\Essentials\StringBuilder;


/**
 * Block that can contain children
 */
abstract class NodeBlock extends Block
{

    protected $children = array();

    public function addChild(Block $item) {
        $this->children[] = $item;
        $item->setParent($this);
    }

    public function removeChild($index) {
        unset($this->children[$index]);
    }

    /**
     * Returns the collection of Blocks belonging to this one
     * @return Block[]
     */
    public function getChildren() {
        return $this->children;
    }
    
    public function getChild($index) {
        return isset($this->children[$index]) ? $this->children[$index] : null;
    }

    public function lastChild() {
        $size = count($this->children);
        if ($size > 0) {
            return $this->children[$size - 1];
        }
        return false;
    }
    
    public function countChildren() {
        return count($this->children);
    }
    
    public function hasChildren() {
        $size = count($this->children);
        return $size > 0;
    }

    /**
     * @return array Array of compiled lines
     */
    protected function compileRealCss(VariableScope $vars) {
        $lines = new StringBuilder();
        foreach ($this->getChildren() as $child) {
            $lines->addLines($child->compileRealCss($vars));
        }
        
        return $lines;
    }

}
