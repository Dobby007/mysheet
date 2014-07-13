<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Traits;


/**
 * Description of RuleParamListTrait
 *
 * @author dobby007
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
        
        $this->getRoot()->getListManager()->iterateList('RuleParam', function($paramClass) use (&$value, &$result) {
            $res = RuleParam::tryParse($paramClass, $value);
            if ($res instanceof RuleParam) {
                $result = $res;
                FuncListManager::stopIteration();
            }
        });
        
        if (!$result) {
            $value = ltrim($value);
            $result = OtherParam::parse($value);
        }
        
        return $result;
    }
}
