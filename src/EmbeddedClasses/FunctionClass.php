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
use MSSLib\Helpers\FunctionModuleHelper;
use MSSLib\Essentials\VariableScope;
use MSSLib\Helpers\MssClassHelper;

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
        $this->name = trim($name);
        return $this;
    }

    public function setArguments(array $arguments) {
        switch ($this->getName()) {
            case 'url':
                $result = count($arguments) > 0 ?
                        MssClassHelper::parseMssClass($arguments[0], array('string', 'nonQuotedString')) : false;
                
                if ($result instanceof MssClass) {
                    $arguments = [$result];
                } else {
                    $arguments = [];
                }
                break;
            default:
                $arguments = array_map(function ($item) {
                    if ($item instanceof MssClass) {
                        return $item;
                    }
                    return MssClassHelper::parseMssClass($item, array('sequence'), true);
                }, $arguments);
                
        }
        
        $this->arguments = $arguments;
        return $this;
    }

    protected function getModuleForFunction(VariableScope $vars) {
        $module = FunctionModuleHelper::findModule($this->getName(), $this->getArguments());
        if ($module !== false) {
            $module->setVars($vars);
        }
        return $module;
    }
    
    public function getValue(VariableScope $vars) {
        $module = $this->getModuleForFunction($vars);
        if ($module=== false) {
            throw new \MSSLib\Error\CompileException(null, 'FUNCTION_NOT_FOUND', [$this->getName()]);
        }
        
        return call_user_func_array([$module, $this->getName()], $this->getArguments());
    }

    public function toRealCss(VariableScope $vars) {
        $module = $this->getModuleForFunction($vars);
        if ($module=== false) {
            return  $this->getName() . 
                    '(' . ArrayHelper::implode_objects(', ', $this->getArguments(), 'toRealCss', $vars) . ')';
        }
        
        return call_user_func_array([$module, $this->getName()], $this->getArguments());
    }
        
    public static function parse(&$string) {
        $string_copy = $string;
        $function = StringHelper::parseFunction($string_copy, true);
        if ($function && ctype_alnum($function['name'])) {
            $string = $string_copy;
            return new self($function['name'], $function['arguments']);
        }
        return false;
    }
}
