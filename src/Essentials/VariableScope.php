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

namespace MSSLib\Essentials;

use MSSLib\Traits\RootClassTrait;
use MSSLib\Helpers\ArrayHelper;
use MSSLib\Error\ParseException;

/**
 * Description of VariableList
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class VariableScope implements \ArrayAccess {
    use RootClassTrait;
    
    private $parentScope;
    private $map = array();
    private $numericVars = false;
    
    public function __construct(VariableScope $parentScope = null) {
        $this->setParentScope($parentScope);
    }

    public function getParentScope() {
        return $this->parentScope ? $this->parentScope : $this->getRoot()->getVars();
    }

    public function setParentScope($parentScope) {
        $this->parentScope = $parentScope;
        return $this;
    }
    
    public function setMap(array $map) {
        foreach ($map as $key => $value) {
            $this->set($key, $value);
        }
    }
    
    public function appendScope(VariableScope $scope) {
        ArrayHelper::concat($this->map, $scope->asArray());;
    }
    
    public function set($name, $value) {
        if (is_null($name)) {
            $this->map[] = $value;
        } else if (self::canBeVariable($name)) {
            if (is_int($name) && !$this->numericVarsEnabled())
                return false;
                
            $this->map[$name] = $value;
        } else {
            throw new ParseException(null, 'BAD_VARIABLE_NAME');
        }
        
        return $this;
    }
    
    public function get($name) {        
        if (isset($this->map[$name])) {
            return $this->map[$name];
        }
        
        if ($this !== $this->getParentScope()) {
            return $this->getParentScope()->get($name);
        }

        
        return null;
    }
    
    public function enableNumericVars($bool) {
        $this->numericVars = !!$bool;
    }
    
    public function numericVarsEnabled() {
        return $this->numericVars;
    }
    
    public function exists($name) {
        return isset($this->map[$name]);
    }
    
    public function offsetExists($offset) {
        return $this->exists($offset);
    }

    public function offsetGet($offset) {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value) {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset) {
        unset($this->map[$offset]);
    }
    
    public function __get($name) {
        return $this->get($name);
    }

    public function __isset($name) {
        return $this->exists($name);
    }

    public function __set($name, $value) {
        $this->set($name, $value);
    }
    
    public function clean() {
        $this->map = [];
    }
    
    public function asArray(callable $filter_function = null) {
        if (is_null($filter_function))
            return $this->map;
        
        return ArrayHelper::filter($this->map, $filter_function);
    }
    
    public static function canBeVariable($name) {
        return preg_match('/[a-z_][a-z0-9_]*|[0-9]+/i', $name);
    }
    
    public static function merge(VariableScope $_scopes = null) {
        $result = new self();
        $args = func_get_args();
        
        foreach ($args as $scope) {
            if ($scope instanceof VariableScope) {
                $result->appendScope($scope);
            }
        }
        
        return $result;
    }
    
    public function createChildScope() {
        $scope = new self();
        return $scope->setParentScope($this);
    }
    
    public function ensureNewScope(VariableScope $scope = null) {
        return $scope ? $scope->createChildScope() : $this->createChildScope();
    }
    
    public static function getInstantiatedScope(VariableScope $scope0 = null, VariableScope $_otherScopes = null) {
        foreach (func_get_args() as $arg) {
            if ($arg instanceof VariableScope) {
                return $arg;
            }
        }
        return null;
    }
}