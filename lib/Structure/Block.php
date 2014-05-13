<?php

namespace MySheet\Structure;

abstract class Block {

    private $childrens = array();
    private $parent = null;

    public function __construct($parent) {
        $this->setParent($parent);
    }

    public function addChild(Block $item) {
        $this->childrens[] = $item;
        $item->setParent($this);
    }

    public function removeChild($index) {
        unset($this->childrens[$index]);
    }

    public function remove() {
        if ($this->parent) {
            $childs = $this->parent->getChildren();
            foreach ($childs as $key => $item) {
                if ($item === $this)
                    $this->parent->removeChild($key);
            }
        }
    }

    public function getChildrens() {
        return $this->childrens;
    }

    public function getParent() {
        return $this->parent;
    }

    public function setParent($parent) {
        if ($parent === null)
            return;

        if ($parent instanceof Block)
            $this->parent = $parent;
    }

    public function getCssPath() {
        return '';
    }

    public function lastChild() {
        $size = count($this->childrens);
        if ($size > 0) {
            return $this->childrens[$size - 1];
        }
        return false;
    }

    /**
     * @return array Array of compiled lines
     */
    protected function compileRealCss() {
        $lines = [];
        foreach ($this->getChildrens() as $child) {
            \MySheet\array_concat($lines, $child->compileRealCss());
        }
        return $lines;
    }

    public function toRealCss($as_array = false) {
        $compiled = $this->compileRealCss();
        if (!is_array($compiled)) {
            return false;
        }

        $result = false;
        if ($as_array === false) {
            $result = implode("\n", $compiled);
        } else {
            $result = $compiled;
        }

        return $result;
    }

}
