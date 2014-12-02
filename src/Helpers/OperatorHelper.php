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

namespace MSSLib\Helpers;

use MSSLib\MySheet;
use MSSLib\Essentials\MathOperator;
use MSSLib\Essentials\FuncListManager;
use MSSLib\Traits\RootClassTrait;

/**
 * Class that helps to work with operators
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
abstract class OperatorHelper
{
    use RootClassTrait;
    
    public static function parseOperator(&$string) {
        return self::getRootObj()->getListManager()->getList('Operator')->iterate(function($operatorClass) use (&$string, &$result) {
            $result = MathOperator::tryParse($operatorClass, $string);
            if ($result instanceof MathOperator) {
                return $result;
            }
        });
    }
}
