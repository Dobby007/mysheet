<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Tools;

use MySheet\Traits\MagicPropsTrait;


/**
 * Description of MSSettings
 *
 * @author dobby007
 */
class MSSettings {
    use MagicPropsTrait;
    
    public $color;
    public $plugins;
    public $cssRenderer;
    public $parser;
    public $cacher;
    public $colorLibs;
    public $import;
    private $_paramPriority;
    
    
    public function __construct(array $settings = []) {
        $this->load($settings);
    }
    
    public function load(array $settings) {
        $default = require(ROOTDIR . 'Config/default' . EXT);
        $settings = array_merge($default, $settings);

        foreach ($settings as $name => $value) {
            $this->$name = $value;
        }
    }
    
    public function get($complex_name, $default = null) {
        $complex_name = explode('.', $complex_name);
        $name = array_shift($complex_name);
        $var = $default;
        
        if (isset($this->$name)) {
            $var = $this->$name;
        } else {
            return $default;
        }
        
        foreach ($complex_name as $name) {
            if (isset($var[$name])) {
                $var = $var[$name];
            } else {
                return $default;
            }
        }
        
        return $var;
    }
    
    public function setParamPriority($paramPriority) {
        if (is_string($paramPriority))
            $paramPriority = preg_split ('/\s*,?\s+/', $paramPriority);
        
        if (is_array($paramPriority))   
            $this->_paramPriority = $paramPriority;
    }
    
    public function getParamPriority() {
        return $this->_paramPriority;
    }
    


    
}
