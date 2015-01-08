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

use MSSLib as MSN;
use MSSLib\MySheet;
use SyHolloway\MrColor\Color;

/**
 * Description of MrColorLib
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class MrColorLib extends ColorLib
{
    /**
     * Internal color object
     * @var Color
     */
    private $color;
    private static $msMap = [
        'red' => 'r',
        'green' => 'g',
        'blue' => 'b',
        'alpha' => 'a',
        'hue' => 'hue',
        'saturation' => 'sat',
        'lightness' => 'lt',
        'brightness' => 'bt'
    ];
    
    
    public function getMrChannel($name) {
        $channel = $this->color->$name;
        if ($name === 'saturation' || $name === 'lightness') {
            return $channel * 100;
        }
        
        return $channel;
    }
    
    public function getChannel($name) {
        $mrName = $this->mrChannelName($name);
        return $name === null ? null : $this->color->$mrName;
    }

    public function getLibName() {
        return 'mrcolor';
    }

    public function setChannel($name, $value) {
        $mrName = $this->mrChannelName($name);
        if ($mrName) {
            $this->color->$mrName = $this->fixColorChannel($name, $value);
            return true;
        }
        return false;
    }
    
    protected function update() {
        switch ($this->type) {
            case self::TRGB:
            case self::TRGBA:
                $this->color = Color::create([
                    'red' => $this->getSourceChannel('r'),
                    'green' => $this->getSourceChannel('g'),
                    'blue' => $this->getSourceChannel('b'),
                    'alpha' => $this->getSourceChannel('a', 1)
                ]);
                break;
            case self::THSL:
            case self::THSLA:
                $this->color = Color::create([
                    'hue' => $this->getSourceChannel('hue'),
                    'saturation' => $this->getSourceChannel('sat') / 100,
                    'lightness' => $this->getSourceChannel('lt') / 100,
                    'alpha' => $this->getSourceChannel('a', 1)
                ]);
                break;
            case self::THEX:
                $this->color = Color::create([
                    'hex' => $this->getSourceChannel(0)
                ]);
                break;
        }
    }
    
    public function transformTo($type) {
        if (!$this->color) {
            return null;
        }
        
        switch ($type) {
            case self::TRGB:
                return $this->makeMsColorArray('red', 'green', 'blue');
            case self::TRGBA:
                return $this->makeMsColorArray('red', 'green', 'blue', 'alpha');
            case self::THSL:
                return $this->makeMsColorArray('hue', 'saturation', 'lightness');
            case self::THSLA:
                return $this->makeMsColorArray('hue', 'saturation', 'lightness', 'alpha');
            case self::THEX:
                return [$this->color->hex];
        }
    }
    
    public function setLibPath($path) {
        $fullPath = MySheet::WORKDIR . MSN\DS . $path . MSN\DS . 'manual-init' . MSN\EXT;
        require_once $fullPath;
    }
    
    
    private function mrChannelName($msName) {
        $index = array_search($msName, self::$msMap, true);
        if ($index !== false) {
            return $index;
        }
        return null;
    }
    
    private function msChannelName($mrName) {
        if (isset(self::$msMap[$mrName])) {
            return self::$msMap[$mrName];
        }
        return null;
    }
    
    private function makeMsColorArray($channel0, $_channels = null) {
        if (!$this->color) {
            return null;
        }
        
        $result = [];
        foreach (func_get_args() as $arg) {
            $result[$this->msChannelName($arg)] = $this->getMrChannel($arg);
        }
        return $result;
    }
}
