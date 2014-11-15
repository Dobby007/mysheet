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

namespace MSSLib\EmbeddedClasses;

use MSSLib\Essentials\MssClass;
use MSSLib\Essentials\IMathSupport;
use MSSLib\Essentials\VariableScope;

/**
 * Class that represents a metric (with its' type) in both MSS and CSS. It is a rule parameter (MssClass).
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class MetricClass extends MssClass implements IMathSupport
{
    const DEFAULT_UNIT = '%';
    
    protected $metric;
    protected $unit;
    
    public function __construct($metric, $unit) {
        $this->setMetric($metric);
        $this->setUnit($unit);
    }
    
    public function getMetric() {
        return $this->metric;
    }
    
    public function getFloatMetric() {
        return $this->getUnit() === '%' ? $this->metric / 100 : $this->metric;
    }

    public function getUnit() {
        return $this->unit;
    }

    public function setMetric($metric) {
        $this->metric = floatval($metric);
        return $this;
    }

    public function setUnit($unit) {
        if (self::isRightUnit($unit)) {
            $this->unit = $unit;
        } else {
            $this->unit = self::DEFAULT_UNIT;
        }
        return $this;
    }
    
    public function toRealCss(VariableScope $vars) {
        return $this->getMetric() . $this->getUnit();
    }
    
    public static function isRightUnit($unit) {
        return ctype_alpha($unit) && strlen($unit) <= 3;
    }
        
    public static function parse(&$string) {
//            var_dump($string);
        if (preg_match('/^(-?(?:\d*\.)?\d+)(\S+)?/i', $string, $matches)) {
            parent::trimStringBy($string, strlen($matches[0]));
            return new self($matches[1], empty($matches[2]) ? '' : $matches[2]);
        }
        return false;
    }
    
    public static function registerOperations() {
        \MSSLib\Operators\PlusOperator::registerCalculationFunction(get_class(), get_class(), function (MetricClass $obj1, MetricClass $obj2) {
            return self::sumTwoMetrics($obj1, $obj2, true);
        });
        \MSSLib\Operators\MinusOperator::registerCalculationFunction(get_class(), get_class(), function (MetricClass $obj1, MetricClass $obj2) {
            return self::sumTwoMetrics($obj1, $obj2, false);
        });
        
        \MSSLib\Operators\MultiplyOperator::registerCalculationFunction(get_class(), get_class(), function (MetricClass $obj1, MetricClass $obj2) {
            $resultMetric = false;
            $resultUnit = false;

            if ($obj1->getUnit() === $obj2->getUnit()) {
                $resultMetric = $obj2->getMetric() * $obj1->getMetric();
                $resultUnit = $obj2->getUnit();
            } else if ($obj2->getUnit() === '%') {
                $resultMetric = $obj1->getMetric() * ($obj1->getMetric() * $obj2->getFloatMetric());
                $resultUnit = $obj1->getUnit();
            } else if ($obj1->getUnit() === '%') {
                $resultMetric = $obj2->getMetric() * ($obj2->getMetric() * $obj1->getFloatMetric());
                $resultUnit = $obj2->getUnit();
            }

            return $resultMetric === false ? null : new self($resultMetric, $resultUnit);
        });
        
        \MSSLib\Operators\DivideOperator::registerCalculationFunction(get_class(), get_class(), function (MetricClass $obj1, MetricClass $obj2) {
            $resultMetric = false;
            $resultUnit = false;

            if ($obj1->getUnit() === $obj2->getUnit()) {
                $resultMetric = $obj2->getMetric() / $obj1->getMetric();
                $resultUnit = $obj2->getUnit();
            } else if ($obj2->getUnit() === '%') {
                $resultMetric = $obj1->getMetric() / ($obj1->getMetric() * $obj2->getFloatMetric());
                $resultUnit = $obj1->getUnit();
            }

            return $resultMetric === false ? null : new self($resultMetric, $resultUnit);
        });
        
        
        
        return true;
    }
    
    /**
     * Sums two metric objects
     * @param \MSSLib\EmbeddedClasses\MetricClass $obj1 First operand
     * @param \MSSLib\EmbeddedClasses\MetricClass $obj2 Second operand
     * @param bool $doSum True to get sum of two metric objects and False to get difference between them
     * @return MetricClass|null
     */
    public static function sumTwoMetrics(MetricClass $obj1, MetricClass $obj2, $doSum = true) {
        $resultMetric = false;
        $resultUnit = false;
        
        if ($obj1->getUnit() === $obj2->getUnit()) {
            $resultMetric = $obj1->getMetric() + ($doSum ? 1 : -1) * $obj2->getMetric();
            $resultUnit = $obj2->getUnit();
        } else if ($obj2->getUnit() === '%') {
            $resultMetric = $obj1->getMetric() + ($doSum ? 1 : -1) * $obj1->getMetric() * $obj2->getFloatMetric();
            $resultUnit = $obj1->getUnit();
        } else if ($obj1->getUnit() === '%') {
            $resultMetric = $obj2->getMetric() + ($doSum ? 1 : -1) * $obj2->getMetric() * $obj1->getFloatMetric();
            $resultUnit = $obj2->getUnit();
        }
        
        return $resultMetric === false ? null : new self($resultMetric, $resultUnit);
    }
}
