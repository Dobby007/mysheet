<?php

namespace MySheet\Structure;

use MySheet\Essentials\VariableScope;

/**
 * Description of RuleValue
 *
 * @author dobby007
 */
class RuleValue {
    private $values = array();
    
    function __construct($value) {
        $this->setValue($value);
    }
    
    public function getParam($index) {
        return isset($values[$index]) ? $values[$index] : false;
    }
    
    public function setParam($index, $param) {
        if (is_string($param)) {
            $this->values[$index] = $param;
            return true;
        }
        return false;
    }

    public function getValue($as_array = false) {
        return $as_array ? $this->values : implode(' ', $this->values);
    }

    public function setValue($values) {
        if (is_string($values)) {
            $values = trim($values);
            preg_match_all('/([^\s]+)(?:[\s;]+$|\s|$)/iU', $values, $matches, PREG_PATTERN_ORDER);
            
//            var_dump($matches);
            $this->values = $matches[1];
        }
    }
    
    public function toRealCss(VariableScope $vars = null) {
        return $this->getValue();
    }
    
    public function __toString() {
        return $this->toRealCss();
    }


}
