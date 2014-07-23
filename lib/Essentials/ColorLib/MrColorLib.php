<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Essentials\ColorLib;
use SyHolloway\MrColor\Color;

/**
 * Description of MrColorLib
 *
 * @author dobby007
 */
class MrColorLib extends ColorLib {
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
        if ($name === 'saturation' || $name === 'lightness')
            return $channel * 100;
        
        return $channel;
    }
    
    public function getChannel($name) {
        $name = $this->mrChannelName($name);
        return $name === null ? null : $this->color->$name;
    }

    public function getLibName() {
        return 'mrcolor';
    }

    public function setChannel($name, $value) {
        
    }
    
    public function update() {
        switch ($this->type) {
            case self::TRGB:
            case self::TRGBA:
                $this->color = Color::create([
                    'red' => $this->getSourceChannel('r'),
                    'green' => $this->getSourceChannel('g'),
                    'blue' => $this->getSourceChannel('b'),
                    'alpha' => $this->getSourceChannel('a', 1)
                ]);
            case self::THSL:
            case self::THSLA:
                $this->color = Color::create([
                    'hue' => $this->getSourceChannel('hue'),
                    'saturation' => $this->getSourceChannel('sat') / 100,
                    'lightness' => $this->getSourceChannel('lt') / 100,
                    'alpha' => $this->getSourceChannel('a', 1)
                ]);
                
            case self::THEX:
                $this->color = Color::create([
                    'hex' => $this->getSourceChannel(0)
                ]);
        }
//        $this->color = Color::create([
//            'red' => 144,
//            'blue' => 70,
//            'green' => 125
//        ]);
//        var_dump($this->color->red, $this->color->green, $this->color->blue, $this->color->hue, $this->color->saturation, $this->color->lightness);
//        $this->color = Color::create([]);
//        $this->color->hue = 0;
//        $this->color->saturation = 1;
//        $this->color->lightness = 0.5;
        
//        var_dump($this->color->hex, $this->color->red, $this->color->green, $this->color->blue, $this->color->hue, $this->color->saturation, $this->color->lightness);
        echo $this->type . ':' . $this->color . "\n\n";
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
        require_once ROOTDIR . $path . DS . 'manual-init' . EXT;
    }
    
    
    private function mrChannelName($msName) {
        $index = array_search($msName, self::$mrMap, true);
        if ($index !== false) {
            return self::$mrMap[$index];
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
            var_dump($arg, $this->color->$arg);
            $result[$this->msChannelName($arg)] = $this->getMrChannel($arg);
        }
        return $result;
    }
}
