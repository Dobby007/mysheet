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

namespace MSSLib\Functionals\RuleParam;

use MSSLib\Essentials\RuleParam;

/**
 * Class that represents a string in rule value (RuleValue). It is a rule parameter (RuleParam).
 * String is always enclosed in qoutes.
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class StringParam extends RuleParam {
    protected $text;
    
    public function __construct($text) {
        $this->setText($text);
    }

    
    public function getText() {
        return $this->text;
    }
    
    public function getQuotedText() {
        return '"' . str_replace('"', '\"', $this->text) . '"';
    }

    public function setText($text) {
        $this->text = $text;
    }

    public function toRealCss() {
        return $this->getQuotedText();
    }
    
    public function __toString() {
        return $this->toRealCss();
    }
        
    public static function parse(&$string) {
        //TODO: consider that the string might have escaped double qoutes
        if (preg_match('/^"(.*)"/', $string, $matches)) {
            parent::trimStringBy($string, strlen($matches[0]));
            return new self($matches[1]);
        }
        return false;
    }
}
