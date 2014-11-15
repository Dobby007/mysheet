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

/**
 * Description of MssClassHelper
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class MssClassHelper
{
    use RootClassTrait;
    
    public static function parseMssClass(&$inputString, callable $filterFunction = null) {
        if (empty($filterFunction)) {
            $filterFunction = function () { return true; };
        }
            
        return self::getRootObj()->getListManager()->getList('MssClass')->iterate(function($paramClass) use (&$inputString, $filterFunction) {
            if (!$filterFunction($paramClass)) {
                return;
            }
            
            $res = MssClass::tryParse($paramClass, $inputString);
            if ($res instanceof MssClass) {
                FuncListManager::stopIteration($res);
            }
        });
    }
}
