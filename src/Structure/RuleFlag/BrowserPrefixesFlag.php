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

namespace MSSLib\Structure\RuleFlag;

use MSSLib\Essentials\RuleFlag\RuleFlagCreator;
use MSSLib\Essentials\RuleFlag\RuleFlag;

/**
 * Description of ImportantFlag
 *
 * @author dobby007
 */
class BrowserPrefixesFlag extends RuleFlag
{
    private $_prefixes = array();
    
    public function __construct(array $prefixes) {
        $this->_prefixes = $prefixes;
    }
    
    public function toRealCss(\MSSLib\Essentials\VariableScope $vars) {
        return null;
    }
    
    public static function creator() {
        return new BrowserPrefixesFlagCreator();
    }
    
    
}

class BrowserPrefixesFlagCreator extends RuleFlagCreator {
    /**
     * @return ImportantFlag|false
     */
    public function createFromArray($flagProps) {
        if ($flagProps['name'] !== 'prefixWith') {
            return false;
        }
        if (!is_array($flagProps['arguments'])){
            return false;
        }
        return new BrowserPrefixesFlag($flagProps['arguments']);
    }
}