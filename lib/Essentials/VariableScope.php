<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Essentials;

use MySheet\Traits\RootClassTrait;

/**
 * Description of VariableList
 *
 * @author dobby007
 */
class VariableScope implements \ArrayAccess {
    use RootClassTrait;
    
    private $map = array();
    
    public function setMap(array $map) {
        foreach ($map as $key => $value) {
            $this->set($key, $value);
        }
    }
    
    public function set($name, $value) {
        if (self::canBeVariable($name))
            $this->map[$name] = $value;
        else
            throw new Exception ('Can not be variable');
        
        return $this;
    }
    
    public function get($name) {
        if (isset($this->map[$name])) {
            return $this->map[$name];
        }
        
        if (isset($this->isRootSet())) {
            return $this->getRoot()->getVars()->get($name);
        }
        
        return null;
    }
    
    public function exists($name) {
        return isset($this->map[$name]);
    }
    
    public function offsetExists($offset) {
        return $this->exists($name);
    }

    public function offsetGet($offset) {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value) {
        if (!is_null($offset)) {
            $this->set($offset, $value);
        }
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
    
    public static function canBeVariable($name) {
        return preg_match('/[a-z_][a-z0-9_]*/i', $name);
    }

}