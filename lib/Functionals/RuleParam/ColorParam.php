<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Functionals\RuleParam;

use MySheet\Essentials\RuleParam;
use MySheet\Helpers\StringHelper;
/**
 * Description of MetricParam
 *
 * @author dobby007
 */
class ColorParam extends RuleParam {
    protected static $allowed_types = ['hex', 'rgb', 'rgba', 'hsb', 'hsba', 'hsl', 'hsla'];
    protected $type;
    protected $color;
    
    public function __construct($type, array $color = []) {
        $this->setType($type);
        $this->setColor($color);
    }
    
    public function getType() {
        return $this->type;
    }

    public function getColor() {
        return $this->color;
    }

    public function setType($type) {
        if (self::isRightType($type)) {
            $this->type = $type;
        } else {
            //throw
        }
    }

    public function setColor($color) {
        if ($this->isCorrectColor($this->getType(), $color)) {
            $this->color = $color;
            
        } else {
            //throw
        }
    }
    
    public function isCorrectColor($type, array $color) {
        return true;
    }

    public function toRealCss() {
        return $this->getType() . implode(', ', $this->getColor());
    }
    
    public function __toString() {
        return $this->toRealCss();
    }
    
    protected static function isRightType($type) {
        return in_array($type, self::$allowed_types);
    }
    
    public static function parseColorString($string) {
        $result = false;
        if ($string[0] === '#') {
            //hex represantation
            $result = [
                'type' => 'hex',
                'color' => [substr($string, 1)]
            ];
            $clen = strlen($result['color'][0]);
                
            if ($clen !== 3 && $clen !== 6) {
                return false;
            }
            
            
        } else {
            $function = StringHelper::parse_function($string);
            $arguments = &$function['arguments'];
            
            foreach ($arguments as &$arg) {
                $metric = StringHelper::parseMetric($arg);
                if ($metric === false) {
                    return false;
                }
                $arg = $metric['metric'];
            }
//            var_dump($function);
            
            //tricky thing to check number of arguments
            if (strlen($function['name']) !== count($arguments)) {
                return false;
            }
            
            $result = [
                'type' => $function['name'],
                'color' => []
            ];
            
            $color = &$result['color'];
            switch ($function['name']) {
                case 'rgb':
                    $color = [
                        'r' => $arguments[0],
                        'g' => $arguments[1],
                        'b' => $arguments[2]
                    ];
                    break;
                case 'rgba':
                    $color = [
                        'r' => $arguments[0],
                        'g' => $arguments[1],
                        'b' => $arguments[2],
                        'a' => $arguments[3]
                    ];
                    break;
            }
            
        }
            
        return $result;
    }
    
    public static function parse(&$string) {
        if (preg_match('/^(#[[:xdigit:]]{3}|#[[:xdigit:]]{6}|(?:rgb|rgba|hsl|hsla|hsb)\(.+\))(?:$|\s)/i', $string, $matches)) {
            $color = self::parseColorString($matches[1]);
            var_dump($color);
            parent::trimStringBy($string, strlen($matches[0]));
            return new self($color['type'], $color['color']);
        }
        return false;
    }
}
