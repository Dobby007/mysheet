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

namespace MSSLib\Essentials;

use MSSLib\Structure\Ruleset;
use MSSLib\Structure\NodeBlock;

/**
 * Description of RulesetFinder
 *
 * @author dobby007
 */
class VariableFinder 
{
    
    /**
     * @return First found variable
     */
    public static function findVariable($selectors, NodeBlock $rootBlock) {
        return self::findVariablesInternal($selectors, $rootBlock, true);
    }

    /**
     * @return  Array of found variables with the given name
     */
    public static function findVariables($selectors, NodeBlock $rootBlock) {
        return self::findVariablesInternal($selectors, $rootBlock, false);
    }
    
    private static function findVariablesInternal($selectors, NodeBlock $rootBlock, $stopOnFirst) {
        // nothing to see here. it's just a stub
    }

}
