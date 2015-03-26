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
 * Description of EventRegistry
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class EventRegistry
{
    private $map = array();
    
    public function triggerEvent($class, $eventName, array $arguments) {
        if (isset($this->map[$class][$eventName])) {
            foreach ($this->map[$class][$eventName] as $callback) {
                call_user_func_array($callback, $arguments);
            }
            return true;
        }
        return false;
    }
    /*
    public function triggerEvent($class, $eventName, $arguments = null, &$handled = null) {
        $handled = false;
        if (isset($this->map[$class][$eventName])) {
            $arguments = array_merge([&$handled], is_array($arguments) ? $arguments : []);
            foreach ($this->map[$class][$eventName] as $callback) {
                $result = call_user_func_array($callback, $arguments);
                if ($handled !== false) {
                    return $result;
                }
            }
        }
        return false;
    }
    */
    public function registerHandler($class, $eventName, callable $callback) {
        if (!isset($this->map[$class])) {
            $this->map[$class] = [];
        }
        
        if (!isset($this->map[$class][$eventName])) {
            $this->map[$class][$eventName] = [];
        }
        
        $this->map[$class][$eventName][] = $callback;
//        var_dump($this->map);
        return $this;
    }
    
    public function on($class, $eventName, callable $callback) {
        return $this->registerHandler($class, $eventName, $callback);
    }
    
    public function fire($class, $eventName, $arguments = null, &$handled = null) {
        $this->triggerEvent($class, $eventName, $arguments, $handled);
    }
}
