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

abstract class NodeBlock extends Block {

    private $children = array();

    public function addChild(Block $item) {
        $this->children[] = $item;
        $item->setParent($this);
    }

    public function removeChild($index) {
        unset($this->children[$index]);
    }

    public function getChildren() {
        return $this->children;
    }

    public function lastChild() {
        $size = count($this->children);
        if ($size > 0) {
            return $this->children[$size - 1];
        }
        return false;
    }

    /**
     * @return array Array of compiled lines
     */
    protected function compileRealCss(VariableScope $vars = null) {
        $lines = new StringBuilder();
//        var_dump(count($this->getChildrens()));
        foreach ($this->getChildren() as $child) {
            $lines->addLines($child->compileRealCss());
        }
        
        return $lines;
    }

}
