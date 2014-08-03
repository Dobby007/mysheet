<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Essentials\ColorLib;

use MySheet\Traits\MagicPropsTrait;

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
        
    public abstract function transformTo($type);
    public abstract function getChannel($name);
    public abstract function setChannel($name, $value);
    public abstract function update();
    public abstract function getLibName();
    
    public function addChannel($name, $value) {
        return $this->setChannel($name, $this->getChannel($name) + $value);
    }
    
    /**
     * 
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
    
    public function getSourceChannel($name, $default = 0) {
        if (isset($this->source_color[$name])) {
            return $this->source_color[$name];
        }
        
        return $default;
    }
    
    public function getColor() {
        return $this->transformTo($this->type);
    }
}
