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
    
    public function setOrder(array $order, callable $equalityCheck = null, callable $mapCallback = null) {
        $available_items = array_map(function ($funcItem) use ($mapCallback) {
            return [$mapCallback !== null ? $mapCallback($funcItem) : $funcItem, $funcItem];
        }, $this->available_items);
        
        foreach ($order as $orderedFunc) {
            foreach ($available_items as $availFunc) {
                if (
                        ($equalityCheck === null && $orderedFunc === $availFunc[0]) || 
                        ($equalityCheck !== null && $equalityCheck($orderedFunc, $availFunc[0])) 
                ) {
                    $this->enabled_items[] = $availFunc[1];
                    break;
                }
            }
        }
    }
    
    public function sortWith(callable $callback = null) {
        if (is_null($callback)) {
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
                $call_result = call_user_func($callback, $functional);
                if (!is_null($call_result)) {
                    return $call_result;
                }
            }
        } catch (StopException $exc) { 
            return $exc->getResult();
        }

        return $default;
    }

    public function getIterator() {
        $this->ensureEnabledItems();
        foreach ($this->enabled_items as $functional) {
            yield $functional;
        }
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