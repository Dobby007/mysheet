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

use MSSLib as MSN;
use MSSLib\MySheet;
use MSSLib\Essentials\MssClass;
use MSSLib\Helpers\SettingsHelper;
use MSSLib\Helpers\StringHelper;
use MSSLib\Essentials\ColorLib\ColorLib;
use MSSLib\Error\SystemException;
use MSSLib\Error\InputException;
use MSSLib\Essentials\VariableScope;
use MSSLib\Essentials\Math\IOperatorRegistrar;

/**
 * Class that represents a color in both MSS and CSS. It is a rule parameter (MssClass).
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class ColorClass extends MssClass implements IOperatorRegistrar
{    
    protected static $allowed_types = ['html', 'hex', 'rgb', 'rgba', 'hsl', 'hsla'];
    protected static $css_supported_types = ['html', 'hex', 'rgbs', 'rgba', 'hsl', 'hsla'];
    protected $type;
    protected $color;
    protected $_colorLib;
    protected static $html_colors = null;
    
    public function __construct($type, array $color = []) {
        $this->setType($type);
        $this->setColor($color);
    }
    
    /**
     * Overrided method
     * @return string
     */
    public function getShortDescription() {
        return 'Color(' . $this->getType() . ', ' . self::colorToString($this->getType(), $this->getColor()) . ')';
    }
    
    /**
     * Gets current library which will be used to manipulate with colors
     * @return \MSSLib\Essentials\ColorLib\ColorLib
     */
    public function getColorLib() {
        $libClass = $this->getSetting('color.lib.class', null);
        if ($libClass && !($this->_colorLib instanceof $libClass)) {
            if (class_exists($libClass)) {
                $this->_colorLib = SettingsHelper::createObjectFromSettings($this->getSetting('color.lib'));
            }
        }
        
        if (!$this->_colorLib) {
            throw new SystemException(null, 'NO_COLOR_LIB');
        }
        
        return $this->_colorLib;
        
    }

    /**
     * Gets current type
     * @return string
     */
    public function getType() {
        return $this->type;
    }
    
    /**
     * Returns type of the color if it is not HTML, otherwise returns ColorLib::THEX
     * @return string
     */
    public function getSafeType() {
        return $this->type === ColorLib::THTML ? ColorLib::THEX : $this->type;
    }
    
    /**
     * Gets current color
     * @return array
     */
    public function getColor() {
        return $this->color;
    }
    
    /**
     * Returns current color if it is not of HTML-type, otherwise converts it to HEX-type
     * @return array
     */
    public function getSafeColor() {
        if ($this->type === ColorLib::THTML) {
            return [self::transformHtmlToHexColor($this->color[0])];
        }
        return $this->color;
    }

    /**
     * Sets a current type of the color
     * @param array $color
     * @throws InputException
     */
    public function setType($type) {
        if (self::isRightType($type)) {
            $this->type = $type;
        } else {
            throw new InputException(null, 'WRONG_COLOR_TYPE');
        }
    }

    /**
     * Sets a current color
     * @param array $color
     * @throws InputException
     */
    public function setColor($color) {
        if (!self::isCorrectColor($this->getType(), $color)) {
            throw new InputException(null, 'WRONG_COLOR_FORMAT');
        }
        if ($this->getType() !== ColorLib::THEX && $this->getType() !== ColorLib::THTML) {
            foreach ($color as $channelName => &$channelValue) {
                $channelValue = ColorLib::fixColorChannel($channelName, $channelValue);
            }
        }
            $this->color = $color;
    }
    
    protected function setResultColor() {
        $resultColor = $this->getColorLib()->getColor();
        
        if ($this->type === ColorLib::THTML) {
            $htmlAnalog = self::transformHexToHtmlColor($resultColor[0]);
            if ($htmlAnalog) {
                $resultColor = [$htmlAnalog];
            } else {
                $this->setType(ColorLib::THEX);
            }
        }
        $this->setColor($resultColor);
    }
    
    protected static function fixValueOfMetricClass(MetricClass $instance) {
        switch ($instance->getUnit()) {
            case 'lt':
            case 'sat':
                return $instance->getMetric() / 100;
            default:
                return $instance->getMetric();
        }
    }
    
    /**
     * Gets a value of specific channel and updates current color
     * @param string $channel
     */
    public function getColorChannel($channel) {
        return $this->getColorLib()->setColor($this->getSafeType(), $this->getSafeColor())->getChannel($channel);
    }
    
    /**
     * Sets a value to the specific channel $channel and ss current color
     * @param string $channel
     */
    public function setColorChannel($channel, $value) {
        $result = $this->getColorLib()->setColor($this->getSafeType(), $this->getSafeColor())->setChannel($channel, $value);
        if ($result === true) {
            $this->setResultColor();
        }
    }
    
    /**
     * Adds a delta-value to specific channel of the color and updates current color
     */
    public function addDeltaToColorChannel($channel, $value) {
        $result = $this->getColorLib()->setColor($this->getSafeType(), $this->getSafeColor())->addChannel($channel, $value);
        if ($result === true) {
            $this->setResultColor();
        }
    }
    
    /**
     * Checks whether current color type has an alpha channel
     */
    public function hasAlphaChannel() {
        return substr($this->getType(), -1) === 'a';
    }
    
    /**
     * Returns boolean which indicates whether color array $color is correct for the type $type
     * @param string $type
     * @param array $color
     * @return boolean
     */
    public static function isCorrectColor($type, array $color) {
        switch ($type) {
            case ColorLib::THTML:
                return self::htmlColorExists($color[0]);
            case ColorLib::THEX:
                return in_array(strlen($color[0]), [3, 6]);
            default:
                return true;
        }
    }
    
    public function toRealCss(VariableScope $vars) {
        $targetType = null;
        $newcolor = $this->getColor();
        $transformMode = $this->getSetting('color.transform', 'unknown');
        if (
            self::isCssSupportedType($this->getType()) && 
            $transformMode === 'unknown'
        ) {
            $targetType = $this->getType();
        } else {
            $cur_type = $this->getType();
            if ($this->hasAlphaChannel()) {
                $targetType = $this->getSetting('color.defaultTypeAlpha',  ColorLib::TRGBA);
            } else {
                $targetType = $this->getSetting('color.defaultType',  ColorLib::THEX);
            }
            if ($cur_type === ColorLib::THTML && !$this->getSetting('color.allowHtmlColorOutput', false)) {
                $cur_type = ColorLib::THEX;
                $newcolor = [self::transformHtmlToHexColor($newcolor[0])];
            } else if ($cur_type === ColorLib::THTML) {
                $targetType = ColorLib::THTML;
            }
            if ($targetType !== $cur_type) {
                $newcolor = $this->getColorLib()->setColor($cur_type, $newcolor)->transformTo($targetType);
            }
        }
        
        return self::colorToString($targetType, $newcolor);
    }
    
    public function __toString() {
        return $this->toRealCss();
    }
    
    public static function registerOperations() {
        \MSSLib\Operators\PlusOperator::registerCalculationFunction(get_class(), 'MSSLib\EmbeddedClasses\MetricClass', function (ColorClass $obj1, MetricClass $obj2) {
            $resultColor = clone $obj1;
            $resultColor->addDeltaToColorChannel($obj2->getUnit(), self::fixValueOfMetricClass($obj2));
            return $resultColor;
        });
        \MSSLib\Operators\MinusOperator::registerCalculationFunction(get_class(), 'MSSLib\EmbeddedClasses\MetricClass', function (ColorClass $obj1, MetricClass $obj2) {
            $resultColor = clone $obj1;
            $resultColor->addDeltaToColorChannel($obj2->getUnit(), -self::fixValueOfMetricClass($obj2));
            return $resultColor;
        });
    }
    
    /**
     * Transforms color array to CSS representation
     * @param string $type
     * @param array $color
     * @return array
     */
    public static function colorToString($type, array $color) {
        switch ($type) {
            case ColorLib::TRGB:
            case ColorLib::TRGBA:
                $arr = [$color['r'], $color['g'], $color['b']];
                if ($type === ColorLib::TRGBA) {
                    $arr[] = $color['a'];
                }
                return $type . '(' . implode(', ', $arr) . ')';
            case ColorLib::THSL:
                return sprintf('hsl(%d, %d%%, %d%%)', $color['hue'], $color['sat'], $color['lt']);
            case ColorLib::THSLA:
                return sprintf('hsla(%d, %d%%, %d%%, %.2f)', $color['hue'], $color['sat'], $color['lt'], $color['a']);
            case ColorLib::THEX:
                return '#' . $color[0];
            case ColorLib::THTML:
                return $color[0];
        }
    }
    
    protected static function isRightType($type) {
        return in_array($type, self::$allowed_types);
    }
    
    protected static function isCssSupportedType($type) {
        return in_array($type, self::$css_supported_types);
    }
    
    /**
     * Parses string with CSS representation of color in hex-format (e.g #abc)
     * @param string $string
     * @return false|array
     */
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
            'type' => ColorLib::THEX,
            'color' => [$hexCode]
        ];
        
    }
    
    /**
     * Parses string with CSS representation of color in function-format (e.g rgb(a,b,c))
     * @param string $string
     * @return false|array
     */
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
            $arg = self::fixPercentageValue($metric);
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
    
    /**
     * Fixes percentage value, transforms it to float value and returns it. If unit of the value is not measured in percents, original value is returned.
     * @param array $value
     * @return float|int
     */
    protected static function fixPercentageValue(array $value) {
        if ($value['unit'] === '%') {
            return $value['metric'] / 100;
        } else {
            return $value['metric'];
        }
    }
    
    /**
     * Parses string with CSS representation of color
     * @param string $string
     * @return false|array
     */
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
    
    public static function transformHtmlToHexColor($name) {
        self::loadHtmlColors();
        if (isset(self::$html_colors[$name])) {
            return self::$html_colors[$name];
        }
        return false;
    }
    
    public static function transformHexToHtmlColor($hex) {
        self::loadHtmlColors();
        return array_search($hex, self::$html_colors);
    }
    
    public static function htmlColorExists($name) {
        return !!self::transformHtmlToHexColor($name);
    }
    
    protected static function loadHtmlColors() {
        if (self::$html_colors === null) {
            self::$html_colors = require_once(MySheet::WORKDIR . MSN\DS . 'Etc' . MSN\DS . 'Includes' . MSN\DS . 'HtmlColors' . MSN\EXT);
        }
    }
    
    public static function parse(&$string) {
        if (preg_match('/^([a-z]+)(?:\s+|$)/i', $string, $matches) && self::htmlColorExists($matches[1])) {
            parent::trimStringBy($string, strlen($matches[0]));
            return new self(ColorLib::THTML, [$matches[1]]);
        } else if (preg_match('/^(#[[:xdigit:]]{3}|#[[:xdigit:]]{6}|(?:rgb|rgba|hsl|hsla)\(.+\))(?:$|\s)/i', $string, $matches)) {
            $color = self::parseColorString($matches[1]);
            if ($color) {
                parent::trimStringBy($string, strlen($matches[0]));
                return new self($color['type'], $color['color']);
            }

        }
        return false;
    }
}
