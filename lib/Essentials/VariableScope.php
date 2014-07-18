<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Essentials;

use MySheet\Traits\RootClassTrait;
use MySheet\Helpers\ArrayHelper;

/**
 * Description of VariableList
 *
 * @author dobby007
 */
class VariableScope implements \ArrayAccess {
    use RootClassTrait;
    
    private $map = array();
    private $numericVars = false;
    
    public function __construct() {
        
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
        if (is_null($name))
            $this->map[] = $value;
        else if (self::canBeVariable($name)) {
            if (is_int($name) && !$this->numericVarsEnabled())
                return false;
                
            $this->map[$name] = $value;
        } else
            throw new Exception ('Can not be variable');
        
        return $this;
    }
    
    public function get($name) {
        if (isset($this->map[$name])) {
            return $this->map[$name];
        }
        
        if ($this !== $this->getRoot()->getVars()) {
            return $this->getRoot()->getVars()->get($name);
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
    
    public static function newScope(VariableScope $scope = null) {
        if ($scope)
            return $scope;
        
        return new self();
    }
    
    public function createScope(VariableScope $scope = null) {
        $scope = self::newScope($scope);
        return $scope;
    }

}