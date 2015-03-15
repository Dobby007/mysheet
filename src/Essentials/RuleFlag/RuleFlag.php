<?php

/*
 * Copyright 2015 dobby007.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace MSSLib\Essentials\RuleFlag;

use MSSLib\Essentials\VariableScope;

/**
 * Class representing rule's flag
 *
 * @author dobby007
 */
abstract class RuleFlag
{
    protected $_name;
    protected $_arguments = false;
    
    /**
     * Compiles this instance of MssClass into MySheet Styles' string
     * @return string
     */
    public function toMss(VariableScope $vars) {}
    
    public static function creator() {
    }
    
    /**
     * Compiles this instance of MssClass into CSS string
     * @return string
     */
    public function toRealCss(VariableScope $vars) {
        if (empty($this->_name)) {
            return '';
        }
        
        $arguments = false;
        if (is_array($this->_arguments)) {
            $arguments = [];
            foreach ($this->getArguments() as $name=>$argument) {
                $arguments[] = (is_string($name) ? $name . ' = ' : '') . $argument->toRealCss($vars);
            }
            
        }
        return '!' . $this->_name . ($arguments === false ? null : '(' . $arguments . ')');
    }
    
    /**
     * @return RuleFlag|false
     */
    public static function parse(&$string) {
        
    }
}
