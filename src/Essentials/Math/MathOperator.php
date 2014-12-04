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
use MSSLib\Essentials\MssClass;

/**
 * Description of MathOperator
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
abstract class MathOperator
{
    private static $operatorsPrecedence = null;
    
    protected static $operatorName = '';
    protected static $operatorSymbol = '';
    
    function __construct() {
        if (!self::$operatorsPrecedence) {
            self::$operatorsPrecedence = self::buildPrecedenceMap(require(MSN\WORKDIR . 'Etc' . MSN\DS  . 'Includes' . MSN\DS . 'OperatorsPrecedence' . MSN\EXT));
        }
    }
    
    /**
     * Calculates two operands
     * @param MssClass $obj1
     * @param MssClass|null $obj2
     * @return mixed Result of calculation
     * @throws \MSSLib\Error\CompileException
     */
    public function calculate($obj1, $obj2 = null) {
        $calcFuncs = static::getCalculationFunctions($obj1, $obj2);
        $result = null;
        foreach ($calcFuncs as $calcFunc) {
            $result = call_user_func_array($calcFunc, func_get_args());
            if ($result !== null) {
                return $result;
            }
        }
        \MSSLib\Tools\Debugger::logObjects($obj1, get_class($this), $obj2);
        
        if ($this instanceof UnaryOperator) {
            throw new \MSSLib\Error\CompileException(null, 'UNSUPPORTED_UNARY_OPERATION', [static::getOperatorSymbol(), $obj1->getShortDescription()]);
        } else {
            throw new \MSSLib\Error\CompileException(null, 'UNSUPPORTED_BINARY_OPERATION', [static::getOperatorSymbol(), $obj1->getShortDescription(), $obj2->getShortDescription()]);
        }
    }
    
    /**
     * Gets priority of current operator
     * @return int|false
     */
    public function getPriority() {
        $operatorName = static::getOperatorName();
        if ($operatorName && isset(self::$operatorsPrecedence[$operatorName])) {
            return self::$operatorsPrecedence[$operatorName];
        }
        return false;
    }
    
    /**
     * Gets current operator name
     * @return string
     */
    public static function getOperatorName() {
        $operatorName = static::$operatorName;
        return $operatorName;
    }
    
    /**
     * Gets current operator name
     * @return string
     */
    public static function getOperatorSymbol() {
        $operatorName = static::$operatorSymbol;
        return $operatorName;
    }
    
    /**
     * Registers calculation function for two types of operands
     * @param type $operandType1
     * @param type $operandType2
     * @param \MSSLib\Essentials\callable $calcFunc
     */
    public static function registerCalculationFunction($operandType1, $operandType2, callable $calcFunc) {
        MySheet::Instance()->getListManager()->getList('operator' . static::getOperatorName())
                ->addFunctional(new MathOperation(static::getOperatorName(), $operandType1, $operandType2, $calcFunc));
    }
    
    /**
     * Gets available calculation functions for two passed operands
     * @param mixed $obj1 First operand
     * @param mixed $obj2 Second operand
     * @return callable[]
     */
    public static function getCalculationFunctions($obj1, $obj2 = null) {
        $operatorName = static::getOperatorName();
        $mathOperations = MySheet::Instance()->getListManager()->getList('operator' . $operatorName)->filter(function (MathOperation $mathOperation) 
                use ($obj1, $obj2, $operatorName) 
        {
            return $mathOperation->compare($operatorName, get_class($obj1), is_null($obj2)? null : get_class($obj2));
        });
        
        return array_map(function (MathOperation $mathOperation) {
            return $mathOperation->getCalculationFunction();
        }, $mathOperations);
    }
    
    /**
     * Parses string if the operator presented in it
     * @param string $paramClass
     * @param string $string
     * @return mixed
     */
    public static function tryParse($paramClass, &$string) {
        $string = ltrim($string);
        if (class_exists($paramClass) && method_exists($paramClass, 'parse')) {
            return $paramClass::parse($string);
        }

        return false;
    }
    
    /**
     * Virtual method that need to be overrided
     */
    public static function parse(&$string) {
        
    }
    
    /**
     * Virtual method that need to be overrided
     */
    public static function operatorSymbol() {
        
    }
    
    private static function buildPrecedenceMap(array $operatorsPrecedence) {
        $precedenceMap = [];
        self::processPrecedenceGroup($operatorsPrecedence, $precedenceMap);
        return $precedenceMap;
    }
    
    private static function processPrecedenceGroup(array $group, array &$precedenceMap, $precedenceValue = false) {
        $group = array_reverse($group);
        foreach ($group as $index => $item) {
            if (is_string($item)) {
                $precedenceMap[$item] = $precedenceValue === false ? $index : $precedenceValue;
            } else if (is_array($item)) {
                self::processPrecedenceGroup($item, $precedenceMap, $index);
            }
        }
    }
}
