<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MSSLib\Functionals\RuleParam;

use MSSLib\Essentials\RuleParam;
use MSSLib\Helpers\StringHelper;
use MSSLib\Helpers\ArrayHelper;
use MSSLib\Essentials\VariableScope;

/**
 * Internal class that allows using functions in rule values. It is a rule parameter (RuleParam).
 * It represents a function in both MSS and CSS.
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class FunctionParam extends RuleParam {
    protected $name;
    protected $arguments;
    
    public function __construct($name, array $arguments = []) {
        $this->setName($name);
        $this->setArguments($arguments);
    }

    public function getName() {
        return $this->name;
    }

    public function getArguments() {
        return $this->arguments;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function setArguments(array $arguments) {
        $arguments = array_map(function ($item) {
            return $this->parseNestedParam($item);
        }, $arguments);
        
        $this->arguments = $arguments;
        return $this;
    }

    public function toRealCss(VariableScope $vars = null) {
        return  $this->getName() . 
                '(' . 
                ArrayHelper::implode_objects(', ', $this->getArguments(), 'toRealCss', $vars) . 
                ')';
    }
    
    public function __toString() {
        return $this->toRealCss();
    }
    
        
    public static function parse(&$string) {
        $string_copy = $string;
        $function = StringHelper::parseFunction($string_copy);
        if ($function) {
            $string = $string_copy;
            return new self($function['name'], $function['arguments']);
        }
        return false;
    }
}
