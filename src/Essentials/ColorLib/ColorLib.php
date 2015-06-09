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

namespace MSSLib\Essentials\ColorLib;

use MSSLib\Etc\Constants;
use MSSLib\Traits\MagicPropsTrait;

/**
 * Description of ColorLib
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
abstract class ColorLib {
    use MagicPropsTrait;
    
    const TRGB = 'rgb', 
          TRGBA = 'rgba',
          THEX = 'hex',
          THSL = 'hsl',
          THSLA = 'hsla',
          THTML = 'html';
    
    protected $type;
            protected $source_color;
        
    /**
     * Tranforms color to specific type
     * @return array Result color in MSS-format
     */
    public abstract function transformTo($type);
    /**
     * Gets channel with the name $name in MSS-format
     */
    public abstract function getChannel($name);
    /**
     * Sets channel with the name $name in MSS-format
     */
    public abstract function setChannel($name, $value);
    /**
     * Updates current color with the values in $source_color array
     */
    protected abstract function update();
    /**
     * Gets library name
     */
    public abstract function getLibName();
    
    /**
     * Adds delta-value $value to specific channel of current color
     * @param string $name
     * @param float $value
     * @return type
     */
    public function addChannel($name, $value) {
        return $this->setChannel($name, $this->getChannel($name) + $value);
    }
    
    /**
     * Sets source color
     * @param string $type
     * @param array $color
     * @return $this
     */
    public function setColor($type, array $color) {
        $this->type = $type;
        $this->source_color = $color;
        $this->update();
        return $this;
    }
    
    /**
     * Gets specific channel from $source_color array
     * @param string $name
     * @param float $default
     * @return type
     */
    public function getSourceChannel($name, $default = 0) {
        if (isset($this->source_color[$name])) {
            return $this->source_color[$name];
        }
        
        return $default;
    }
    
    /**
     * Gets current color in MSS-format after all transformations
     * @return array
     */
    public function getColor() {
        return $this->transformTo($this->type);
    }
    
    /**
     * Fixes specific channel of color based on its' fixed min and max values
     * @param string $channel
     * @param float $value
     * @return float
     */
    public static function fixColorChannel($channel, $value) {
        switch ($channel) {
            case Constants::CHANNEL_RED:
            case Constants::CHANNEL_GREEN:
            case Constants::CHANNEL_BLUE:
                $value = min([max([$value, 0]), 255]);
                break;
            case Constants::CHANNEL_HUE:
                $value = min([max([$value, 0]), 360]);
                if ($value === 360) {
                    $value = 0;
                }
                break;
            case Constants::CHANNEL_SATURATION:
            case Constants::CHANNEL_LIGHTNESS:
            case Constants::CHANNEL_ALPHA:
                $value = min([max([$value, 0]), 1]);
                break;
        }
        return $value;
    }

    /**
     * Converts relative channel value specified in percents to absolute one
     * @param string $channel Channel name
     * @param float $percentage Relative channel value (must be specified from 0 to 1)
     * @return float
     */
    public static function getChannelAbsoluteValue($channel, $percentage) {
        $percentage = min([max([$percentage, 0]), 1]);
        switch ($channel) {
            case Constants::CHANNEL_RED:
            case Constants::CHANNEL_GREEN:
            case Constants::CHANNEL_BLUE:
                return $percentage * 255;
            case Constants::CHANNEL_HUE:
                return $percentage * 360;
            case Constants::CHANNEL_SATURATION:
            case Constants::CHANNEL_LIGHTNESS:
            case Constants::CHANNEL_ALPHA:
            default:
                return $percentage;
        }

    }
}
