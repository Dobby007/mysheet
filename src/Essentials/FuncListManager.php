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

use MSSLib\Essentials\FuncList;
use MSSLib\Error\StopException;

/**
 * Description of FuncListManager
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class FuncListManager
{
    const PRIORITY_LOWEST = 0,
          PRIORITY_LOW = 1,
          PRIORITY_NORMAL = 2,
          PRIORITY_HIGH = 3,
          PRIORITY_HIGHEST = 4;
    
    /**
     *
     * @var FuncList[]
     */
    protected $lists = [];
    
    /**
     * 
     * @param string $name
     * @return boolean
     */
    protected function createList($name) {
        if (!$this->listExists($name)) {
            $this->lists[$name] = new FuncList();
            return true;
        }
        return false;
        
    }
    
    /**
     * 
     * @param string $listname
     * @return bool
     */
    public function listExists($listname) {
        return isset($this->lists[$listname]);
    }
    
    /**
     * 
     * @param string $listname
     * @return FuncList
     */
    public function getList($listname) {
        $this->createList($listname);
        return $this->lists[$listname];
    }
    
    public static function stopIteration($result = null) {
        throw new StopException($result);
    }
}