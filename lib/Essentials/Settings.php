<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Essentials;

/**
 * Description of Settings
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
abstract class Settings {
    
    
    
    
    public function __get($name) {
        $method_name = 'get' . ucfirst($name);
        if (method_exists($this, $method_name)) {
            return $this->$method_name();
        } else {
            throw new Exception('Undefined property: $' . $name);
        }
    }
    
    public function __set($name, $value) {
        $method_name = 'set' . ucfirst($name);
        var_dump($method_name);
        if (method_exists($this, $method_name)) {
            return $this->$method_name($value);
        } else {
            throw new Exception('Undefined property: $' . $name);
        }
    }
    
    

    /*
    public function __call($name, $arguments) {
        $method_prefix = substr($name, 0, 3);
        if ($method_prefix === 'get') {
            $property = lcfirst(substr($name, 3));
            return $this->$property;
        } else if ($method_prefix === 'set') {
            $property = lcfirst(substr($name, 3));
            $this->$property = reset($arguments);
            return $this;
        }
    }
    */
}
