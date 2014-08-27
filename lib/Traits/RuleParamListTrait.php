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

namespace MSSLib\Traits;

use MSSLib\Error\ParseException;

/**
 * Description of RuleParamListTrait
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
trait RuleParamListTrait {
    public function getParam($index) {
        return isset($this->params[$index]) ? $this->params[$index] : false;
    }
    
    /**
     * @return RuleParam[]
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
        
        
        if ($param instanceof RuleParam) {
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
        $result = false;
        
        $this->getRoot()->getListManager()->getList('RuleParam')->iterate(function($paramClass) use (&$value, &$result) {
            $res = RuleParam::tryParse($paramClass, $value);
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
}
