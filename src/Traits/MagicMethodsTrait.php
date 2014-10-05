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
        $prefix = substr($method_name, 0, 3);
        $propName = lcfirst(substr($method_name, 3));
        
        if (method_exists($this, $method_name)) {
            return $this->$method_name();
        } else if (isset($this->$propName)) {
            if ($prefix === 'get') {
                return $this->$propName;
            } else {
                if (!isset($arguments[0])) {
                    throw new Exception('Value must be set for property `' . $propName . '`');
                }
                $this->__magicSetProperty($propName, $arguments[0]);
                return $this;
            }
        } else {
            throw new \Exception('Undefined property: $' . $propName);
        }
    }
    
    /**
     * Method is called when user tries to set a value to a property through its' magic "get" and "set" methods
     * @param string $propName
     * @param mixed $propValue
     * @return boolean
     */
    protected function __magicSetProperty($propName, $propValue) {
        $this->$propName = $propValue;
        return true;
    }
}
