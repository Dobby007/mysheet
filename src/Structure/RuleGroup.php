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
