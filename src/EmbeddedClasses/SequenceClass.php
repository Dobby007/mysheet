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
use MSSLib\Helpers\ArrayHelper;
use MSSLib\Helpers\StringHelper;
use MSSLib\Essentials\VariableScope;
use MSSLib\Essentials\DelimitedString;

/**
 * Class that represents a sequence of rule parameters delimited by commas.
 * Class is also a rule parameter (MssClass) and can be used in RuleValue instance as a rule parameter.
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class SequenceClass extends MssClass {
    protected $delimitedString;
    protected $delimiter;
    
    public function __construct(array $list, $delimiter) {
        $this->setItemList($list);
        $this->setDelimiter($delimiter);
    }

    /**
     * 
     * @return DelimitedString
     */
    public function getDelimitedString() {
        return $this->delimitedString;
    }

    public function setItemList($list) {
        //call protected function from outside
        $this->delimitedString = new DelimitedString($list, function ($item) {
            return $this->parseNestedParam($item);
        });
        return $this;
    }
    
    public function getDelimiter() {
        return $this->delimiter;
    }

    public function setDelimiter($delimiter) {
        $this->delimiter = $delimiter;
        return $this;
    }
    
    public function toRealCss(VariableScope $vars) {
        return ArrayHelper::implode_objects($this->getDelimiter(), $this->getDelimitedString()->getList(), 'toRealCss', $vars);
    }
    
    public function __toString() {
        return $this->toRealCss();
    }
    
    protected static function splitIntoList(&$string, &$metDelimiter) {
        $stringCopy = $string;
        $list = StringHelper::parseSplittedString($stringCopy, [',', '/'], true, $metDelimiter);
        
        if (count($list) > 1) {
//            var_dump('Source String: ', $string, 'Comma List: ', $commaList);
            $string = $stringCopy;
            return $list;
        }
        
        return false;
    }
    
    public static function parse(&$string) {
        //if it is a MathExpr we should skip and move on
        if (in_array($string[0], ['(', ')'])) {
            return false;
        }
        
        if (strpos($string, ',') === false && strpos($string, '/') === false) {
            return false;
        }
        
        $stringCopy = $string;
        if (is_array(StringHelper::parseFunction($stringCopy, true, true))) {
            return false;
        }
        
        $list = self::splitIntoList($string, $metDelimiter);
        if (is_array($list)) {
            return new self($list, $metDelimiter);
        }
        return false;
    }
}
