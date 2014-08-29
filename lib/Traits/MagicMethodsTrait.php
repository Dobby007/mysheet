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

namespace MSSLib\Traits;

/**
 * Description of MagicMethodsTrait
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
trait MagicMethodsTrait {
    
    public function __call($method_name, array $arguments) {
        $prefix = substr($name, 0, 3);
        $propName = lcfirst(substr($name, 3));
        
        if (method_exists($this, $method_name)) {
            return $this->$method_name();
        } else if (isset($this->$propName)) {
            if ($prefix === 'get') {
                return $this->$propName;
            } else {
                if (!isset($arguments[0])) {
                    throw new Exception('Value must be set for property `' . $propName . '`');
                }
                $this->$propName = $arguments[0];
                return $this;
            }
        } else {
            throw new \Exception('Undefined property: $' . $name);
        }
    }
    
//    protected abstract function canNotFindProperty($name);
}
