<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Structure;

/**
 * Description of PathGroup
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class RuleGroup {
    private $rules = array();

    public function addRule($name, $value) {
        $this->rules[] = [$name, $value];
        return $this;
    }
    
    public function getRules() {
        return $this->rules;
    }

    public function join($ruleSeparator = ': ', $lineSeparator = ";\n", $linePrefix = '    ') {
        $result = '';
        array_walk($this->getLines($ruleSeparator), function($value, $key) use(&$result,  $lineSeparator, $linePrefix) {
            $result .= $linePrefix . $value . $lineSeparator;
            
        });
        return $result;
    }
    
    public function getLines($ruleSeparator = ': ') {
        $result = [];
        array_walk($this->rules, function($value, $key) use(&$result, $ruleSeparator) {
            $result[] = $value[0] . $ruleSeparator . $value[1];
        });
        return $result;
    }
    
    public function __toString() {
        return $this->join();
    }

}
