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

/**
 * Class that represents a color in both MSS and CSS. It is a rule parameter (MssClass).
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class ColorClass extends MssClass 
{    
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
            throw new SystemException(null, 'NO_COLOR_LIB');
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
            throw new InputException(null, 'WRONG_COLOR_TYPE');
        }
    }

    public function setColor($color) {
        if ($this->isCorrectColor($this->getType(), $color)) {
            $this->color = $color;
        } else {
            throw new InputException(null, 'WRONG_COLOR_FORMAT');
        }
    }
    
    /**
     * Checks whether current color type has an alpha channel
     */
    public function hasAlphaChannel() {
        return substr($this->getType(), -1) === 'a';
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
            if ($cur_type === ColorLib::THTML) {
                $cur_type = ColorLib::THEX;
                $newcolor = [self::getHtmlColor($newcolor[0])];
            }
            $type = $this->getSetting('color.defaultType', $this->hasAlphaChannel() ? ColorLib::TRGBA : ColorLib::THEX);
            $newcolor = $this->getColorLib()->setColor($cur_type, $newcolor)->transformTo($type);
        }
        
        return self::colorToString($type, $newcolor);
    }
    
    public function __toString() {
        return $this->toRealCss();
    }
    
    public static function colorToString($type, array $color) {
        switch ($type) {
            case ColorLib::TRGB:
            case ColorLib::TRGBA:
                $arr = [$color['r'], $color['g'], $color['b']];
                if ($type === ColorLib::TRGBA) {
                    $arr[] = $color['a'] / 100;
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
            $html_colors = require_once(MySheet::WORKDIR . MSN\DS . 'Etc' . MSN\DS . 'Includes' . MSN\DS . 'HtmlColors' . MSN\EXT);
        }
        if (isset($html_colors[$name])) {
            return $html_colors[$name];
        }
        return false;
    }
    
    public static function parse(&$string) {
        if (preg_match('/^([a-z]+)/i', $string, $matches) && ($hcolor = self::getHtmlColor($matches[1]))) {
            parent::trimStringBy($string, strlen($matches[0]));
            return new self(ColorLib::THTML, [$matches[1]]);
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
