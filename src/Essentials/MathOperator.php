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

use MSSLib as MSN;
use MSSLib\MySheet;
use MSSLib\Essentials\MathOperation;

/**
 * Description of MathOperator
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
abstract class MathOperator
{
    private static $operatorsPrecedence = null;
    
    function __construct() {
        if (!self::$operatorsPrecedence) {
            self::$operatorsPrecedence = array_reverse(require(MSN\WORKDIR . 'Config' . MSN\DS . 'OperatorsPrecedence' . MSN\EXT));
        }
    }
    
    public function calculate($obj1, $obj2 = null) {
        $getFuncName = get_class($this) . '::getCalculationFunction';
        $func = call_user_func($getFuncName, $obj1, $obj2);
        if ($func === false) {
            throw new \MSSLib\Error\CompileException(null, 'UNSUPPORTED_OPERATION', [self::operatorName()]);
        }
        
        return call_user_func_array($func, func_get_args());
    }
    
    public function getPriority() {
        $operatorName = self::getOperatorNameByClassName(get_class($this));
        if ($operatorName) {
            $priority = array_search($operatorName, self::$operatorsPrecedence, true);
            return $priority;
        }
        return false;
    }
    
    public static function operatorName() {
        $operatorName = self::getOperatorNameByClassName(get_called_class());
        return $operatorName;
    }
    
    public static function getOperatorNameByClassName($className) {
        if (preg_match('/([a-z]+)Operator$/i', $className, $matches)) {
            $operatorName = lcfirst($matches[1]);
            return $operatorName;
        }
        return false;
    }
    
    public static function registerCalculationFunction($operandType1, $operandType2, callable $calcFunc) {
        MySheet::Instance()->getListManager()->getList('operator' . self::operatorName())->addFunctional(new MathOperation(self::operatorName(), $operandType1, $operandType2, $calcFunc));
    }
    
    public static function getCalculationFunction($obj1, $obj2 = null) {
        $result = false;
        $operatorName = self::operatorName();
        MySheet::Instance()->getListManager()->getList('operator' . $operatorName)->iterate(function (MathOperation $mathOperation) 
                use (&$result, $obj1, $obj2, $operatorName) 
        {
            if ($mathOperation->compare($operatorName, get_class($obj1), get_class($obj2))) {
                $result = $mathOperation->getCalculationFunction();
                FuncListManager::stopIteration();
            }
        });
        return $result;
    }
    
    public static function tryParse($paramClass, &$string) {
        $callable = $paramClass . '::' . 'parse';
        $string = ltrim($string);
        if (class_exists($paramClass) && is_callable($callable)) {
            return $paramClass::parse($string);
        }

        return false;
    }
    
    public static function parse(&$string) {
        
    }
    
    public static function operatorSymbol() {
        
    }
}
