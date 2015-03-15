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
use MSSLib\Helpers\ArrayHelper;
use MSSLib\Traits\RootClassTrait;
use MSSLib\Error\ParseException;
use MSSLib\Helpers\MssClassHelper;
use MSSLib\Helpers\RuleFlagHelper;
use MSSLib\Structure\Declaration;
use MSSLib\Essentials\RuleFlag\RuleFlag;
/**
 * Class that represents rule value with its' rule parameters (MssClass)
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class RuleValue
{
    use RootClassTrait;
    
    private $params = array();
    private $_parentDeclaration;
    private $_flags = array();
    
    
    function __construct($value, Declaration $parentDeclaration = null) {
        $this->setValue($value);
        $this->setParentDeclaration($parentDeclaration);
    }
    
    public function getParentDeclaration() {
        return $this->_parentDeclaration;
    }

    public function setParentDeclaration($parentDeclaration) {
        $this->_parentDeclaration = $parentDeclaration;
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
    
    public function parseFlag(&$value) {
        $result = RuleFlagHelper::parseRuleFlag($value);
        if (!$result) {
            throw new ParseException(null, 'FLAG_NOT_PARSED');
        }
        return $result;
    }
    
    public function getFlags() {
        return $this->_flags;
    }

    public function setFlags($flags) {
        $this->_flags = $flags;
        return $this;
    }
    
    public function setFlag($index, $flag) {
        if (is_string($flag)) {
            $flag = $this->parseFlag($flag);
        }
        
        if ($flag instanceof RuleFlag) {
            if ($index === null) {
                $this->_flags[] = $flag;
            } else {
                $this->_flags[$index] = $flag;
            }
            
            return true;
        }
        
        return false;
    }
    
    public function addFlag($flag) {
        return $this->setFlag(null, $flag);
    }
    
    public function getCompiledParams(VariableScope $vars) {
        //we use our own array_map clone here because of php strange behaviour: exceptions can't get out of this function
        $classes = array_filter(ArrayHelper::map(function(MssClass $item) use($vars) {
            return $item->toRealCss($vars);
        }, $this->params));
        
        return $classes;
    }
    
    public function getCompiledFlags(VariableScope $vars) {
        //we use our own array_map clone here because of php strange behaviour: exceptions can't get out of this function
        $flags = array_filter(ArrayHelper::map(function(RuleFlag $item) use($vars) {
            return $item->toRealCss($vars);
        }, $this->_flags));
        
        return $flags;
    }
    
    public function getValue(VariableScope $vars) {
        $classes = $this->getCompiledParams($vars);
        $flags = $this->getCompiledFlags($vars);

        return implode(' ', $classes) . (count($flags) > 0 ? ' ' . implode(' ', $flags) : '');
    }

    public function setValue($value) {
        if (is_string($value)) {
            $value = trim($value);
            
            //clean rule value from garbage
            preg_match_all('/([^\s]+)(?:[\s;]+$|\s|$)/iU', $value, $matches, PREG_PATTERN_ORDER);
            $value = implode(' ', $matches[1]);
            
            while (is_string($value) && strlen($value) > 0) {
                if ($value[0] === '!') {
                    $this->addFlag($this->parseFlag($value));
                } else {
                    $this->addParam($this->parseParam($value));
                }
                $value = ltrim($value);
            }
        }
    }
    /**
     * Parses string and returns array of flags contained in it
     * @param string $ruleFlagsStr
     * @return RuleFlag[]
     */
    protected static function parseRuleFlagsString($ruleFlagsStr) {
        $ruleFlags = [];
        while (!empty($ruleFlagsStr)) {
            $res = RuleFlagHelper::parseRuleFlag($ruleFlagsStr);
            if (!$res) {
                $ruleFlags[] = $res;
            }
        }
        return $ruleFlags;
    }
    public function toRealCss(VariableScope $vars) {
        return $this->getValue($vars);
    }

}
