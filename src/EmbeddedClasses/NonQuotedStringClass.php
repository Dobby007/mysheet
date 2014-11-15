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

namespace MSSLib\EmbeddedClasses;

use MSSLib\Essentials\MssClass;
use MSSLib\Essentials\VariableScope;

/**
 * Class that represents all other rule parameters (MssClass) that do not match requirements of any other rule parameter (MssClass).
 * It is used to represent some text that is not StringParam. The difference between StringParam and this class is 
 * that string is always enclosed with quotes (both single or double quotes) and the text inside this class is not.
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class NonQuotedStringClass extends MssClass {
    protected $text;
    
    public function __construct($text) {
        $this->setText($text);
    }

    
    public function getText() {
        return $this->text;
    }

    public function setText($text) {
        $this->text = $text;
    }

        
    public function toRealCss(VariableScope $vars) {
        return $this->getText();
    }
    
    public static function isRightUnit($unit) {
        return true;
    }
        
    public static function parse(&$string) {
//            var_dump($string);
        if (preg_match('/^(\S+)/', $string, $matches)) {
            parent::trimStringBy($string, strlen($matches[0]));
            return new self($matches[1]);
        }
        return false;
    }
}
