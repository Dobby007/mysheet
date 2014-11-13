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

use MSSLib\Error\StopException;

/**
 * Description of FuncListManager
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class FuncList {
    
    protected $available_items = [];
    protected $enabled_items = null;
    
    
    public function addFunctional($func) {
        $this->available_items[] = $func;
    }
    
    public function setOrder(array $order, callable $equalityCheck = null) {
        if (!is_callable($equalityCheck)) {
            $equalityCheck = function ($a, $b) {
                return $a === $b;
            };
        }
        
        foreach ($order as $orderedFunc) {
            foreach ($this->available_items as $avail_func) {
                if ($equalityCheck($orderedFunc, $avail_func)) {
                    $this->enabled_items[] = $avail_func;
                    break;
                }
            }
        }
//        var_dump($this->available_items, $this->enabled_items);
    }
    
    public function sortWith(callable $callback = null) {
        if (!is_callable($callback)) {
            $callback = function($a, $b) {
                if ($a[0] === $b[0])
                    return 0;
                else
                    return $a > $b ? 1 : -1;
            };
        }
        
        usort($this->available_items[$listname], $callback);
    }
    
    public function iterate(callable $callback, $default = false) {
        $this->ensureEnabledItems();
        try {
            foreach ($this->enabled_items as $functional) {
                call_user_func($callback, $functional);
            }
        } catch (StopException $exc) { 
            return $exc->getResult();
        }

        return $default;
    }
    
    public function map(callable $callback) {
        $this->ensureEnabledItems();
        return array_map($callback, $this->enabled_items);
    }
    
    public function filter(callable $callback) {
        $this->ensureEnabledItems();
        return array_filter($this->enabled_items, $callback);
    }
    
    protected function ensureEnabledItems() {
        if ($this->enabled_items === null) {
            $this->enabled_items = $this->available_items;
        }
    }
    
    public static function stopIteration() {
        throw new StopException();
    }
}