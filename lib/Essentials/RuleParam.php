<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Essentials;

use MySheet\Traits\RootClassTrait;
use MySheet\MySheet;

/**
 * Description of RuleParam
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
abstract class RuleParam {
    use RootClassTrait;
    
    public function __toString() {
        return $this->toRealCss();
    }
    
    /**
     * @return string
     */
    abstract public function toRealCss();

    protected function parseNestedParam($string) {
        $result = null;
        $this->getRoot()->getListManager()->getList('RuleParam')->iterate(function ($paramClass) use ($string, &$result) {
            if ($paramClass == get_class($this)) {
                return;
            }
            
            $res = RuleParam::tryParse($paramClass, $string);
            if ($res instanceof RuleParam) {
                $result = $res;
                FuncListManager::stopIteration();
            }
        });
        
        if (!$result) {
            //throw
        }
        
        return $result;
    }
    
    /**
     * @return RuleParam|false
     */
    public static function parse(MySheet $rootInstance, &$string) { }
    
    public static function trimStringBy(&$string, $length) {
        $string = substr($string, $length);
        if (!is_string($string)) {
            $string = '';
        }
        
    }
    
        public static function tryParse($paramClass, &$string) {
        $callable = $paramClass . '::' . 'parse';
        $string = ltrim($string);
//        var_dump($callable);
        if (class_exists($paramClass) && is_callable($callable)) {
            return $paramClass::parse($string);
        }
        
        return false;
    }
}
