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
     * @return StringBuilder Array of compiled lines
     */
    protected function compileRealCss(VariableScope $vars) {
        return null;
    }

    public function toMss() {
        
    }
    
    /**
     * Compiles CSS and returns it as a StringBuilder class
     * @return StringBuilder Array of compiled lines
     */
    public function toRealCss(VariableScope $vars = null) {
        $autoload_enabled = self::getRootObj()->getSettings()->get('system.internal_autoload', true);
        if ($autoload_enabled === true) {
            self::getRootObj()->getAutoload()->registerAutoload();
        }
        $this->cssRenderingStartedEvent($this);
        
        if ($vars) {
            self::getRootObj()->getVars()->appendScope($vars);
        }
        /* @var $compiled StringBuilder */
        $compiled = $this->compileRealCss(VariableScope::getInstantiatedScope($vars, self::getRootObj()->getVars())->createChildScope());
        if (!($compiled instanceof StringBuilder)) {
            return null;
        }
        
        $this->cssRenderingEndedEvent($this);
        return $compiled->join();
    }
}
