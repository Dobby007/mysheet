<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Traits;

/**
 * Description of RootClassTrait
 *
 * @author dobby007
 */
trait MagicPropsTrait {
    
    public function __get($name) {
        $method_name = 'get' . ucfirst($name);
        if (method_exists($this, $method_name)) {
            return $this->$method_name();
        } else {
            throw new \Exception('Undefined property: $' . $name);
        }
    }
    
    public function __set($name, $value) {
        $method_name = 'set' . ucfirst($name);
//        var_dump($method_name);
        if (method_exists($this, $method_name)) {
            return $this->$method_name($value);
        } else {
            throw new \Exception('Undefined property: $' . $name);
        }
    }
    
}
