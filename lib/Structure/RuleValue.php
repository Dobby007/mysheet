<?php

namespace MySheet\Structure;

use MySheet\Essentials\VariableScope;
use MySheet\Essentials\RuleParam;
use MySheet\Functionals\RuleParam\OtherParam;
use MySheet\Essentials\FuncListManager;
use MySheet\Traits\RootClassTrait;

/**
 * Description of RuleValue
 *
 * @author dobby007
 */
class RuleValue {
    use RootClassTrait;
    
    private $params = array();
    
    function __construct($value) {
        $this->setValue($value);
    }
    
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
            //throw
        }
        
        return $result;
    }
    
    public function getValue(VariableScope $vars = null, $as_array = false) {
        $result = array_map(function(RuleParam $item) use($vars) {
            /*
            if ($item instanceof \MySheet\Functionals\RuleParam\VariableParam) {
                return $item->toRealCss($vars);
            } else {
                return $item->toRealCss();
            }
             
             */
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
    
    public function toRealCss(VariableScope $vars = null) {
        return $this->getValue($vars);
    }
    
    public function __toString() {
        return $this->toRealCss();
    }

}
