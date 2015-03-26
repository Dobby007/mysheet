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

namespace MSSLib\Helpers;

use MSSLib\Traits\RootClassTrait;
use MSSLib\Essentials\MssClass;
use MSSLib\Essentials\FuncListManager;
use MSSLib\Essentials\FunctionModule;

/**
 * Description of MssClassHelper
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
abstract class FunctionModuleHelper
{
    use RootClassTrait;
    
    /**
     * Finds a function to execute against supplied function's name and arguments
     * @param string $functionName
     * @param array $arguments
     * @return FunctionModule Reference to a function in a string representation or false if function can not be found
     */
    public static function findModule($functionName, $arguments) {
        return self::msInstance()->getListManager()->getList('FunctionModule')->iterate(function($functionModule) use ($functionName, $arguments) {
            if ($functionModule->hasDeclaredFunction($functionName, $arguments)) {
                return $functionModule;
            }
        });
    }
    
    
}
