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

namespace MSSLib\Plugins\Mixin;

use MSSLib\Structure\Declaration;
use MSSLib\Structure\LeafBlock;
use MSSLib\Traits\RootClassTrait;
use MSSLib\Traits\PluginClassTrait;
use MSSLib\Essentials\VariableScope;
use MSSLib\Structure\RuleGroup;


/**
 * Description of Mixin
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class Mixin extends LeafBlock {
    use RootClassTrait, PluginClassTrait;
    
    protected $declarations = array();
    protected $name;
    protected $locals = array();
    
    
    /**
     * Creates a new builder for Mixin
     * @return \MSSLib\Plugins\Mixin\MixinBuilder
     */
    public static function builder() {
        return new MixinBuilder();
    }
    
    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }
    
    public function getLocals() {
        return $this->locals;
    }
    
    public function addLocal($name) {
        if (!array_search($name, $this->locals))
            $this->locals[] = $name;
        //else //throw
        return $this;
    }
    
    public function setLocals(array $locals) {
        $this->locals = array_unique($locals);
        return $this;
    }
    
    /**
     * 
     * @return Declaration[]
     */
    public function getDeclarations() {
        return $this->declarations;
    }
    
    public function countDeclarations() {
        return count($this->declarations);
    }
    
    public function addDeclaration($declaration) {
        if (is_string($declaration)) {
            $declaration = new Declaration($declaration);
        }
        
        if ($declaration instanceof Declaration) {
            $this->declarations[] = $declaration;
        }
        return $this;
    }
    
    public function addDeclarations($declarations) {
        if (is_string($declarations)) {
            $declarations = preg_split('/(?![\s;]+$);/', $declarations);
        }
        
        if (is_array($declarations)) {
            foreach ($declarations as $declaration) {
                $this->addDeclaration($declaration);
            }
        }
        return $this;
    }
    
    protected function compileRealCss(VariableScope $vars) {
        $this->plugin->registerMixin($this);
        return [];
    }
    
    public function render(VariableScope $arguments = null) {
        $renderScope = VariableScope::merge($arguments);
        $renderScope['arguments'] = $renderScope->asArray(function($varname) {
            return is_int($varname);
        });
        
        foreach ($this->locals as $index => $local) {
            if (!isset($renderScope[$index])) {
                break;
            }
            $renderScope[$local] = $renderScope[$index];
        }
        
        $rendered_rules = new RuleGroup();
        foreach ($this->getDeclarations() as $declaration) {
            $rendered_rules->addRule($declaration->getRuleName(), $declaration->getRuleValue()->getValue($renderScope));
        }
        return $rendered_rules;
    }
}


class MixinBuilder
{
    private $_localParams = [];
    private $_children = [];
    private $_name;
    
    public function __construct() {
        
    }
    
    public function setName($name) {
        $this->_name = $name;
        return $this;
    }
    
    public function addLocalParameter($name, $default = null) {
        $this->_localParams[] = $name;
        return $this;
    }
    
    public function setLocalParameters(array $locals) {
        $this->_localParams = $locals;
        return $this;
    }
    
    public function addDeclaration($declaration) {
        $this->_children[] = $declaration;
        return $this;
    }
    
    public function addDeclarations($declarations) {
        $this->_children = array_merge($this->_children, $declarations);
        return $this;
    }
    
    public function getResult() {
        $mixin = new Mixin(null);
        $mixin->setName($this->_name)
              ->setLocals($this->_localParams)
              ->addDeclarations($this->_children);
        return $mixin;
    }

}