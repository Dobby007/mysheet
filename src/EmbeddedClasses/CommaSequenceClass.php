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
use MSSLib\Traits\MssClassListTrait;
use MSSLib\Essentials\DelimitedString;

/**
 * Class that represents a sequence of rule parameters delimited by commas.
 * Class is also a rule parameter (MssClass) and can be used in RuleValue instance as a rule parameter.
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class CommaSequenceClass extends MssClass {
    use MssClassListTrait;
    
    protected $delimitedString;
    
    public function __construct(array $list) {
        $this->setItemList($list);
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
    }
    
    public function toRealCss(VariableScope $vars = null) {
        return ArrayHelper::implode_objects(', ', $this->getDelimitedString()->getList(), 'toRealCss', $vars);
    }
    
    public function __toString() {
        return $this->toRealCss();
    }
    
    protected static function splitCommaList(&$string) {
        $stringCopy = $string;
        $commaList = StringHelper::parseSplittedString($stringCopy, ',');
        
        
        if (count($commaList) > 1) {
//            var_dump('Source String: ', $string, 'Comma List: ', $commaList);
            $string = $stringCopy;
            return $commaList;
        }
        
        
        
        return false;
    }
    
    public static function parse(&$string) {
        $commaList = self::splitCommaList($string);
        if (is_array($commaList)) {
            return new self($commaList);
        }
        return false;
    }
}
