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

/**
 * Description of Settings
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
abstract class Settings
{
    
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
}
