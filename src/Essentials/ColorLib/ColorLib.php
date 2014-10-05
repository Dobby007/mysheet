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
