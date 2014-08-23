<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MSSLib\Essentials;

use MSSLib\Essentials\FuncList;
use MSSLib\Error\StopException;

/**
 * Description of FuncListManager
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class FuncListManager {
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
    
    public function addFunctional($listname, $func, $priority = self::PRIORITY_NORMAL) {
        $this->createList($listname);
        $this->lists[$listname][] = [$priority, $func];
        usort($this->lists[$listname], function($a, $b) {
            if ($a[0] === $b[0])
                return 0;
            else
                return $a > $b ? 1 : -1;
        });
    }
    
    public function iterateList($listname, callable $callback) {
        if ($this->listExists($listname)) {
            try {
                foreach ($this->lists[$listname] as $functional) {
                    call_user_func($callback, $functional[1]);
                }
            } catch (StopException $exc) { }
            
            return true;
        }
        return false;
    }
    
    public static function stopIteration() {
        throw new StopException();
    }
}