<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MSSLib\Functionals\RuleParam;

use MSSLib\Essentials\RuleParam;

/**
 * Class that represents a metric (with its' type) in both MSS and CSS. It is a rule parameter (RuleParam).
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class MetricParam extends RuleParam {
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

    public function getUnit() {
        return $this->unit;
    }

    public function setMetric($metric) {
        $this->metric = intval($metric);
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
        return true;
    }
        
    public static function parse(&$string) {
//            var_dump($string);
        if (preg_match('/^(-?(?:\d*\.)?\d+)(em|px|%|ex|in|cm|mm|pt|pc)?/i', $string, $matches)) {
            parent::trimStringBy($string, strlen($matches[0]));
            return new self($matches[1], empty($matches[2]) ? '' : $matches[2]);
        }
        return false;
    }
}
