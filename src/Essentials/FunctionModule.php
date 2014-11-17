<?php

/*
 * Copyright 2014 dobby007 (Alexander Gilevich, alegil91@gmail.com).
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

namespace MSSLib\Essentials;

use MSSLib\Essentials\VariableScope;

/**
 * Description of FunctionModule
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
abstract class FunctionModule
{
    private $vars;
    
    public static function __call($name, $arguments) {
        if (self::hasDeclaredFunction($name, $arguments)) {
            call_user_func_array(__CLASS__ . '::' . $name, $arguments);
        }
    }
    
    public function hasDeclaredFunction($name, $arguments) {
//        $funcName = __CLASS__ . '::' . $name;
        if (method_exists($this, $name)) {
            return true;
        }
        return false;
    }
    
    /**
     * Returns scope of vars that was set by setVars method
     * @return VariableScope
     */
    public function getVars() {
        return $this->vars;
    }

    /**
     * Saves scope of vars for internal use
     * @param VariableScope $vars
     * @return $this
     */
    public function setVars(VariableScope $vars) {
        $this->vars = $vars;
        return $this;
    }

}
