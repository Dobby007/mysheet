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

namespace MSSLib\EmbeddedClasses;

use MSSLib\Essentials\MssClass;
use MSSLib\Helpers\StringHelper;
use MSSLib\Helpers\ArrayHelper;
use MSSLib\Essentials\VariableScope;

/**
 * Internal class that allows using functions in rule values. It is a rule parameter (MssClass).
 * It represents a function in both MSS and CSS.
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class FunctionClass extends MssClass {
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

    public function toRealCss(VariableScope $vars) {
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
        if ($function && ctype_alnum($function['name'])) {
            $string = $string_copy;
            return new self($function['name'], $function['arguments']);
        }
        return false;
    }
}
