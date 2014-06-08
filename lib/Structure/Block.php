<?php

namespace MySheet\Structure;

use MySheet\Helpers\ArrayHelper;
use MySheet\Traits\RootClassTrait;
use MySheet\Traits\HandlerCallTrait;
use MySheet\Essentials\VariableScope;

abstract class Block {

    use RootClassTrait, HandlerCallTrait;

    private $parent = null;

    public function __construct($parent) {
        $this->setParent($parent);
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

    public function getParent() {
        return $this->parent;
    }

    public function setParent($parent) {
        if ($parent instanceof NodeBlock)
            $this->parent = $parent;
    }

    /**
     * @return array Array of compiled lines
     */
    protected function compileRealCss(VariableScope $vars = null) {
        return [];
    }

    public function toMss() {
        
    }
    
    public function toRealCss($as_array = false) {
        $this->getRoot()->getAutoload()->registerAutoload();
        $this->cssRenderingStartedEvent($this);
        
        
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

        $this->cssRenderingEndedEvent($this);
        $this->getRoot()->getAutoload()->restoreAutoload();
        return $result;
    }

}
