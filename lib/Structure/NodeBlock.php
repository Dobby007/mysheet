<?php

namespace MySheet\Structure;

use MySheet\Structure\Block;
use MySheet\Helpers\ArrayHelper;
use MySheet\Essentials\VariableScope;
use MySheet\Essentials\StringBuilder;

abstract class NodeBlock extends Block {

    private $children = array();

    public function addChild($item) {
        if ($item instanceof NodeBlock || $item instanceof LeafBlock) {
            $this->children[] = $item;
            $item->setParent($this);
        } else {
            //throw
        }
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
