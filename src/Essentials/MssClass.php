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
use MSSLib\Essentials\VariableScope;
use MSSLib\Helpers\MssClassHelper;

/**
 * Description of MssClass
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
abstract class MssClass
{
    use RootClassTrait;

    public function __toString() {
        return $this->toRealCss(self::getRootObj()->getVars());
    }

    /**
     * Gets inner or current MssClass object depending on type of derivative class
     * @param \MSSLib\Essentials\VariableScope $vars
     * @return \MSSLib\Essentials\MssClass
     */
    public function getValue(VariableScope $vars) {
        return $this;
    }
    
    /**
     * Compiles this instance of MssClass into MySheet Styles' string
     * @return string
     */
    public function toMss(VariableScope $vars) {}
    
    /**
     * Compiles this instance of MssClass into CSS string
     * @return string
     */
    abstract public function toRealCss(VariableScope $vars);

    protected function parseNestedParam(&$string, $filterThisClass = true) {
        $result = MssClassHelper::parseMssClass($string, $filterThisClass ? function ($mssClass) {
            return $mssClass !== get_class($this);
        } : null);

        if (!$result) {
            throw new ParseException(null, 'PARAM_NOT_PARSED');
        }

        return $result;
    }

    /**
     * @return MssClass|false
     */
    public static function parse(&$string) {
        
    }

    public static function trimStringBy(&$string, $length) {
        $string = substr($string, $length);
        if (!is_string($string)) {
            $string = '';
        }
    }

    public static function tryParse($paramClass, &$string) {
        $callable = $paramClass . '::' . 'parse';
        $string = ltrim($string);
        if (class_exists($paramClass) && is_callable($callable)) {
            return $paramClass::parse($string);
        }

        return false;
    }

}
