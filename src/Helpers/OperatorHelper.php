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
use MSSLib\Essentials\Math\MathOperator;
use MSSLib\Essentials\FuncListManager;
use MSSLib\Traits\RootClassTrait;
use MSSLib\Essentials\TypeClassReference;
/**
 * Class that helps to work with operators
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
abstract class OperatorHelper
{
    use RootClassTrait;
    
    public static function parseOperator(&$inputString) {
        return self::getRootObj()->getListManager()->getList('Operator')->iterate(function(TypeClassReference $operatorClassRef) use (&$inputString) {
            $operatorClass = $operatorClassRef->getFullClass();
            $res = null;
            if (class_exists($operatorClass) && method_exists($operatorClass, 'parse')) {
                $res = $operatorClass::parse($inputString);
            }
            return $res instanceof MathOperator ? $res : null;
        });
    }
}
