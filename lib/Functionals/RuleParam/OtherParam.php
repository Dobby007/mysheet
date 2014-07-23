<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Functionals\RuleParam;

use MySheet\Essentials\RuleParam;

/**
 * Class that represents all other rule parameters (RuleParam) that do not match requirements of any other rule parameter (RuleParam).
 * It is used to represent some text that is not StringParam. The difference between StringParam and this class is 
 * that string is always enclosed with quotes (both single or double quotes) and the text inside this class is not.
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class OtherParam extends RuleParam {
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

        
    public function toRealCss() {
        return $this->getText();
    }
    
    public function __toString() {
        return $this->toRealCss();
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
