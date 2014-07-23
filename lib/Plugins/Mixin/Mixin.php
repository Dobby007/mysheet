<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Plugins\Mixin;

use MySheet\Structure\Declaration;
use MySheet\Structure\LeafBlock;
use MySheet\Traits\RootClassTrait;
use MySheet\Traits\PluginClassTrait;
use MySheet\Essentials\VariableScope;
use MySheet\Structure\RuleGroup;


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
        
//        var_dump($renderScope);
        
        $rendered_rules = new RuleGroup();
        foreach ($this->getDeclarations() as $declaration) {
//            var_dump('DECLNAME: ' . $declaration->getRuleName());
            $rendered_rules->addRule($declaration->getRuleName(), $declaration->getRuleValue()->getValue($renderScope));
        }
        var_dump($rendered_rules);
        return $rendered_rules;
    }
}
