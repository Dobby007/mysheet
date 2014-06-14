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
 * @author dobby007
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
