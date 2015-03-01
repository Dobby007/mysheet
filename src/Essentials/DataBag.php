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

/**
 * DataBag represents a namespace in which one can hold data associated with current instance of MySheet class
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class DataBag implements \ArrayAccess {
    use RootClassTrait;
    
    private $_data = array();
    
    public function set($name, $value) {
        $this->_data[$name] = $value;
        
        return $this;
    }
    
    public function get($name) {        
        if (isset($this->_data[$name])) {
            return $this->_data[$name];
        }
        
        if ($this !== $this->getParentScope()) {
            return $this->getParentScope()->get($name);
        }

        
        return null;
    }
    
    public function exists($name) {
        return isset($this->_data[$name]);
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
        unset($this->_data[$offset]);
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
        $this->_data = [];
    }
}