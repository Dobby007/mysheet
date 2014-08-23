<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MSSLib\Tools;

use MSSLib as MS;
use MSSLib\Traits\MagicPropsTrait;

/**
 * Description of MSSettings
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 * @property string[] ruleParams
 * @property string[] parserExtensions
 */
class MSSettings
{
    use MagicPropsTrait;
    
    public $color;
    public $plugins;
    public $cssRenderer;
    public $parser;
    public $cacher;
    public $colorLibs;
    public $import;
    public $language;
    private $_ruleParams;
    private $_parserExtensions;
    
    public function __construct(array $settings = []) {
        $this->load($settings);
    }
    
    public function load(array $settings) {
        $default = require(MS\WORKDIR . MS\DS . 'Config' . MS\DS . 'DefaultSettings' . MS\EXT);
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
    
    protected function convertPrioritySettingToArray($prioritySetting) {
        if (is_string($prioritySetting)) {
            $prioritySetting = preg_split ('/\s*,?\s+/', $prioritySetting);
        }
        return $prioritySetting;
        
    }

    public function setRuleParams($paramPriority) {
        $paramPriority = $this->convertPrioritySettingToArray($paramPriority);
        if (is_array($paramPriority)) {
            $this->_ruleParams = $paramPriority;
        }
        return $this;
    }
    
    public function getRuleParams() {
        return $this->_ruleParams;
    }
    
    public function getParserExtensions() {
        return $this->_parserExtensions;
    }

    public function setParserExtensions($parserExtensions) {
        $parserExtensions = $this->convertPrioritySettingToArray($parserExtensions);
        if (is_array($parserExtensions)) {
            $this->_parserExtensions = $parserExtensions;
        }
        return $this;
    }


    
}
