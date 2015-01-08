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
use MSSLib\Essentials\TypeClassReference;

/**
 * Description of MssClassHelper
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class MssClassHelper
{
    use RootClassTrait;
    
    public static function parseMssClass(&$inputString, $filter = null, $ignoreFilter = false) {
        if (empty($filter)) {
            $filter = function () { return true; };
        }
        
        if (is_array($filter)) {
            $mssClasses = [];
            foreach ($filter as $mssClass) {
                if (!class_exists($mssClass, false)) {
                    $mssClasses['MSSLib\\EmbeddedClasses\\' . ucfirst($mssClass) . 'Class'] = true;
                }
            }
            $filter = function ($mssClass) use ($mssClasses, $ignoreFilter) {
                $issetResult = isset($mssClasses[$mssClass->getFullClass()]);
                return $ignoreFilter ? !$issetResult : $issetResult;
            };
        }
        
        $inputString = ltrim($inputString);
        return self::getRootObj()->getListManager()->getList('MssClass')->iterate(function(TypeClassReference $mssClass) use (&$inputString, $filter) {
            if (!$filter($mssClass)) {
                return;
            }
            $res = null;
            $mssClassName = $mssClass->getFullClass();
            if (class_exists($mssClassName) && method_exists($mssClassName, 'parse')) {
                $res = $mssClassName::parse($inputString);
            }
            return $res instanceof MssClass ? $res : null;
        });
    }
}
