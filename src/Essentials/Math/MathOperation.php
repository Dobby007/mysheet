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

/**
 * Description of MathOperation
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class MathOperation {
    private $operandType1, $operandType2;
    private $operator;
    private $calculationFunction;
    
    public function __construct($operator, $operandType1, $operandType2, $calculationFunction) {
        $this->operandType1 = $operandType1;
        $this->operandType2 = $operandType2;
        $this->operator = $operator;
        $this->calculationFunction = $calculationFunction;
    }

    
    public function getOperandType1() {
        return $this->operandType1;
    }

    public function getOperandType2() {
        return $this->operandType2;
    }

    public function getOperator() {
        return $this->operator;
    }

    public function getCalculationFunction() {
        return $this->calculationFunction;
    }
    
    public function setOperandType1($operandType1) {
        $this->operandType1 = $operandType1;
    }

    public function setOperandType2($operandType2) {
        $this->operandType2 = $operandType2;
    }

    public function setOperator($operator) {
        $this->operator = $operator;
    }

    public function setCalculationFunction($calculationFunction) {
        $this->calculationFunction = $calculationFunction;
    }

    
    public function compare($operator, $operandType1, $operandType2) {
        return $this->getOperator() === $operator &&
               $this->getOperandType1() === $operandType1 &&
               $this->getOperandType2() === $operandType2;
    }

}
