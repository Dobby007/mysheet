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

namespace MSSLib\Structure;

use MSSLib\Essentials\VariableScope;
use MSSLib\Essentials\MssClass;
use MSSLib\Essentials\FuncListManager;
use MSSLib\Helpers\ArrayHelper;
use MSSLib\Traits\RootClassTrait;
use MSSLib\Error\ParseException;
use MSSLib\Helpers\MssClassHelper;

/**
 * Class that represents rule value with its' rule parameters (MssClass)
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class RuleValue
{
    use RootClassTrait;
    
    private $params = array();
    
    function __construct($value) {
        $this->setValue($value);
    }
    
    public function getParam($index) {
        return isset($this->params[$index]) ? $this->params[$index] : false;
    }
    
    /**
     * @return MssClass[]
     */
    public function getParams() {
        return $this->params;
    }
    
    public function countParams() {
        return count($this->params);
    }
    
    public function setParam($index, $param) {
        if (is_string($param)) {
            $param = $this->parseParam($param);
        }
        
        
        if ($param instanceof MssClass) {
            if ($index === null) {
                $this->params[] = $param;
            } else {
                $this->params[$index] = $param;
            }
            
            return true;
        }
        
        return false;
    }
    
    public function addParam($param) {
        return $this->setParam(null, $param);
    }

    public function parseParam(&$value) {
        $result = MssClassHelper::parseMssClass($value);
        
        if (!$result) {
            throw new ParseException(null, 'PARAM_NOT_PARSED');
        }
        
        return $result;
    }
    
    public function getValue(VariableScope $vars, $as_array = false) {
        //we use our own array_map clone here because of php strange behaviour: exceptions can't get out of this function
        $result = ArrayHelper::map(function(MssClass $item) use($vars) {
            return $item->toRealCss($vars);
        }, $this->params);
        
        return $as_array ? $result : implode(' ', $result);
    }

    public function setValue($value) {
        if (is_string($value)) {
            $value = trim($value);
            
            //clean rule value from garbage
            preg_match_all('/([^\s]+)(?:[\s;]+$|\s|$)/iU', $value, $matches, PREG_PATTERN_ORDER);
            $value = implode(' ', $matches[1]);
            
            
            while (is_string($value) && strlen($value) > 0) {
                $this->addParam($this->parseParam($value));
            }
        }
    }
    
    public function toRealCss(VariableScope $vars) {
        return $this->getValue($vars);
    }

}
