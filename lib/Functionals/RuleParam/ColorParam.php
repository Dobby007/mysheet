<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Functionals\RuleParam;

use MySheet as MSN;
use MySheet\Essentials\RuleParam;
use MySheet\Helpers\StringHelper;
use MySheet\Helpers\SettingsHelper;


/**
 * Class that represents a color in both MSS and CSS. It is a rule parameter (RuleParam).
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class ColorParam extends RuleParam {
    
    protected static $allowed_types = ['html', 'hex', 'rgb', 'rgba', 'hsl', 'hsla'];
    protected static $css_supported_types = ['html', 'hex', 'rgbs', 'rgba', 'hsl', 'hsla'];
    protected static $html_colors;
    protected $type;
    protected $color;
    protected $_colorLib;
    
    public function __construct($type, array $color = []) {
        $this->setType($type);
        $this->setColor($color);
    }
    
    /**
     * 
     * @return \MySheet\Essentials\ColorLib
     */
    public function getColorLib() {
        $libClass = $this->getSetting('color.lib.class', null);
        if ($libClass && !($this->_colorLib instanceof $libClass)) {
            if (class_exists($libClass)) {
                $this->_colorLib = SettingsHelper::createObjectFromSettings($this->getSetting('color.lib'));
            }
        }
        if (!$this->_colorLib) {
            //throw
        }
        
        return $this->_colorLib;
        
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
        $type = null;
        $newcolor = $this->getColor();
        if (
            self::isCssSupportedType($this->getType()) && 
            $this->getSetting('color.transform', 'unknown') === 'unknown'
        ) {
            $type = $this->getType();
        } else {
            $cur_type = $this->getType();
            if ($cur_type === 'html') {
                $cur_type = 'hex';
                $newcolor = [self::getHtmlColor($newcolor[0])];
            }
            $type = $this->getSetting('color.defaultType', 'hex');
            $newcolor = $this->getColorLib()->setColor($cur_type, $newcolor)->transformTo($type);
        }
        
        return self::colorToString($type, $newcolor);
    }
    
    public function __toString() {
        return $this->toRealCss();
        new FunctionParam();
    }
    
    public static function colorToString($type, array $color) {
        switch ($type) {
            case 'rgb':
            case 'rgba':
                $arr = [$color['r'], $color['g'], $color['b']];
                if ($type === 'rgba') {
                    $arr[] = $color['a'];
                }
                return $type . '(' . implode(', ', $arr) . ')';
            case 'hsl':
                return sprintf('hsl(%d, %d%%, %d%%)', $color['hue'], $color['sat'], $color['lt']);
            case 'hsla':
                return sprintf('hsla(%d, %d%%, %d%%, %.2f)', $color['hue'], $color['sat'], $color['lt'], $color['a']);
            case 'hex':
                return '#' . $color[0];
            case 'html':
                return $color[0];
        }
    }
    
    protected static function isRightType($type) {
        return in_array($type, self::$allowed_types);
    }
    
    protected static function isCssSupportedType($type) {
        return in_array($type, self::$css_supported_types);
    }
    
    protected static function parseHexColorString($string) {
        $hexCode = substr($string, 1);
        $clen = strlen($hexCode);
        if ($clen === 3) {
            $hexCode = $hexCode[0] . $hexCode[0] .
                       $hexCode[1] . $hexCode[1] .
                       $hexCode[2] . $hexCode[2];
        }

        if ($clen !== 3 && $clen !== 6) {
            return false;
        }

        return [
            'type' => 'hex',
            'color' => [$hexCode]
        ];
        
    }
    
    protected static function parseFunctionColorString($string) {
        $function = StringHelper::parseFunction($string);
        
        if (!$function) {
            return false;
        }

        $arguments = &$function['arguments'];

        foreach ($arguments as &$arg) {
            $metric = StringHelper::parseMetric($arg);
            if ($metric === false) {
                return false;
            }
            $arg = $metric['metric'];
        }

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
            case 'hsl':
                $color = [
                    'hue' => $arguments[0],
                    'sat' => $arguments[1],
                    'lt' => $arguments[2]
                ];
                break;
            case 'hsla':
                $color = [
                    'hue' => $arguments[0],
                    'sat' => $arguments[1],
                    'lt' => $arguments[2],
                    'a' => $arguments[3]
                ];
                break;
        }

        return $result;
        
    }
    
    public static function parseColorString($string) {
        $result = false;
        if ($string[0] === '#') {
            //hex represantation
            $result = self::parseHexColorString($string);
        } else {
            //representation in rgb, hsb, etc.
            $result = self::parseFunctionColorString($string);
        }
        return $result;
    }
    
    public static function getHtmlColor($name) {
        static $html_colors = null;
        if ($html_colors === null) {
            $html_colors = require_once(ROOTDIR . 'Includes' . DS . 'HtmlColors' . MSN\EXT);
        }
        if (isset($html_colors[$name])) {
            return $html_colors[$name];
        }
        return false;
    }
    
    public static function parse(&$string) {
        if (preg_match('/^([a-z]+)/i', $string, $matches) && ($hcolor = self::getHtmlColor($matches[1]))) {
            parent::trimStringBy($string, strlen($matches[0]));
            return new self('html', [$matches[1]]);
        } else if (preg_match('/^(#[[:xdigit:]]{3}|#[[:xdigit:]]{6}|(?:rgb|rgba|hsl|hsla|hsb)\(.+\))(?:$|\s)/i', $string, $matches)) {
            $color = self::parseColorString($matches[1]);
            if ($color) {
                parent::trimStringBy($string, strlen($matches[0]));
                return new self($color['type'], $color['color']);
            }

        }
        return false;
    }
}
