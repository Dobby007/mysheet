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

namespace MSSLib\Functionals\RuleParam;

use MSSLib\Essentials\RuleParam;
use MSSLib\Essentials\IMathSupport;

/**
 * Class that represents a metric (with its' type) in both MSS and CSS. It is a rule parameter (RuleParam).
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class MetricParam extends RuleParam implements IMathSupport
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
    
    public function toRealCss() {
        return $this->getMetric() . $this->getUnit();
    }
    
    public function __toString() {
        return $this->toRealCss();
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
        \MSSLib\Operators\PlusOperator::registerCalculationFunction(get_class(), get_class(), function (MetricParam $obj1, MetricParam $obj2) {
            return self::sumTwoMetrics($obj1, $obj2, true);
        });
        \MSSLib\Operators\MinusOperator::registerCalculationFunction(get_class(), get_class(), function (MetricParam $obj1, MetricParam $obj2) {
            return self::sumTwoMetrics($obj1, $obj2, false);
        });
        
        \MSSLib\Operators\MultiplyOperator::registerCalculationFunction(get_class(), get_class(), function (MetricParam $obj1, MetricParam $obj2) {
            $resultMetric = false;
            $resultUnit = false;

            if ($obj1->getUnit() === $obj2->getUnit()) {
                $resultMetric = $obj2->getMetric() * $obj1->getMetric();
                $resultUnit = $obj2->getUnit();
            } else if ($obj2->getUnit() === '%') {
                $resultMetric = $obj1->getMetric() * $obj1->getMetric() * $obj2->getFloatMetric();
                $resultUnit = $obj1->getUnit();
            } else if ($obj1->getUnit() === '%') {
                $resultMetric = $obj2->getMetric() * $obj2->getMetric() * $obj1->getFloatMetric();
                $resultUnit = $obj2->getUnit();
            }

            return $resultMetric === false ? null : new self($resultMetric, $resultUnit);
        });
        
        \MSSLib\Operators\DivideOperator::registerCalculationFunction(get_class(), get_class(), function (MetricParam $obj1, MetricParam $obj2) {
            $resultMetric = false;
            $resultUnit = false;

            if ($obj1->getUnit() === $obj2->getUnit()) {
                $resultMetric = $obj2->getMetric() / $obj1->getMetric();
                $resultUnit = $obj2->getUnit();
            } else if ($obj2->getUnit() === '%') {
                $resultMetric = $obj1->getMetric() / $obj1->getMetric() * $obj2->getFloatMetric();
                $resultUnit = $obj1->getUnit();
            }

            return $resultMetric === false ? null : new self($resultMetric, $resultUnit);
        });
        
        
        
        return true;
    }
    
    /**
     * Sums two metric objects
     * @param \MSSLib\Functionals\RuleParam\MetricParam $obj1 First operand
     * @param \MSSLib\Functionals\RuleParam\MetricParam $obj2 Second operand
     * @param bool $doSum True to get sum of two metric objects and False to get difference between them
     * @return MetricParam|null
     */
    public static function sumTwoMetrics(MetricParam $obj1, MetricParam $obj2, $doSum = true) {
        $resultMetric = false;
        $resultUnit = false;
        
        if ($obj1->getUnit() === $obj2->getUnit()) {
            $resultMetric = $obj2->getMetric() + ($doSum ? 1 : -1) * $obj1->getMetric();
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
