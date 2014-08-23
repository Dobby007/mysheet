<?php

namespace MSSLib\Structure;

use MSSLib\Helpers\ArrayHelper;
use MSSLib\Traits\RootClassTrait;
use MSSLib\Traits\HandlerCallTrait;
use MSSLib\Essentials\VariableScope;
use MSSLib\Essentials\StringBuilder;

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

    public function getDepth() {
        $block = $this;
        $depth = 0;
        while ($block->getParent() instanceof Block) {
            $block = $block->getParent();
            $depth++;
        }
        return $depth;
    }
    
    /**
     * @return array Array of compiled lines
     */
    protected function compileRealCss(VariableScope $vars = null) {
        return null;
    }

    public function toMss() {
        
    }
    
    public function toRealCss($as_array = false, $autoload = true) {
        if ($autoload === true) {
            $this->getRoot()->getAutoload()->registerAutoload();
            $this->cssRenderingStartedEvent($this);
        }
        
        /* @var $compiled StringBuilder */
        $compiled = $this->compileRealCss();
        if (!($compiled instanceof StringBuilder)) {
            return null;
        }

        $result = false;
        if ($as_array === false) {
            $result = $compiled->join();
        } else {
            $result = $compiled;
        }

        if ($autoload === true) {
            $this->cssRenderingEndedEvent($this);
            $this->getRoot()->getAutoload()->restoreAutoload();
        }
        return $result;
    }

}
