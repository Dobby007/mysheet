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

use MSSLib\MySheet;
use MSSLib\Traits\RootClassTrait;
use MSSLib\Error\ParseException;

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
            throw new ParseException(null, 'PARAM_NOT_PARSED');
        }
        
        return $result;
    }
    
    /**
     * @return RuleParam|false
     */
    public static function parse(&$string) { }
    
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
