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

namespace MSSLib\Operators;

use MSSLib\Essentials\Math\UnaryOperator;

/**
 * Description of PlusOperator
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class UnaryPlusOperator extends UnaryOperator
{
    protected static $operatorName = 'unaryPlus';
    protected static $operatorSymbol = '+';
    protected static $binaryAnalog = 'plus';
    
    
    public static function parse(&$string) {
        if (substr($string, 0, 1) === self::getOperatorSymbol() && !ctype_space(substr($string, 1, 1))) {
            $string = substr($string, 1);
            return new self();
        }
    }
}
