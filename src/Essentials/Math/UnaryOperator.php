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

namespace MSSLib\Essentials\Math;

use MSSLib as MSN;
use MSSLib\MySheet;
use MSSLib\Essentials\Math\MathOperation;

/**
 * Description of MathOperator
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
abstract class UnaryOperator extends MathOperator
{
    protected static $binaryAnalog = null;
    
    public static function hasBinaryAnalog() {
        return !empty(static::$binaryAnalog);
    }
    
    public static function getBinaryAnalogClass() {
        if (!class_exists(static::$binaryAnalog)) {
            return '\\MSSLib\\Operators\\' . ucfirst(static::$binaryAnalog) . 'Operator';
        } else {
            return static::$binaryAnalog;
        }
    }
    
    public function toBinaryAnalog() {
        if (self::hasBinaryAnalog()) {
            $class = self::getBinaryAnalogClass();
            return new $class();
        }
        return false;
    }
    
    public static function canBeUnaryOperator($string) {
        return substr($string, 0, 1) === self::getOperatorSymbol() && 
               !ctype_space(substr($string, 1, 1));
    }
}
