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
    
    public function __construct(PluginMixin $plugin, $name, array $locals = []) {
        $this->setPlugin($plugin);
        $this->setName($name);
        $this->setLocals($locals);
        
    }
    
    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }
    
    public function getLocals() {
        return $this->locals;
    }
    
    public function addLocal($name) {
        if (!array_search($name, $this->locals))
            $this->locals[] = $name;
        //else //throw
    }
    
    public function setLocals(array $locals) {
        $this->locals = array_unique($locals);
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
            $this->declarations[] = new Declaration($declaration);
        }
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
    }
    
    protected function compileRealCss(VariableScope $vars = null) {
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
