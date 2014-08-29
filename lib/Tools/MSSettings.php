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

namespace MSSLib\Tools;

use MSSLib as MS;
use MSSLib\Traits\MagicPropsTrait;
use MSSLib\Traits\MagicMethodsTrait;

/**
 * Description of MSSettings
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 * @property string[] ruleParams
 * @property string[] parserExtensions 
 * @method this setColor(array $colorOptions) Sets instructions for the lib on how to work with colors
 * @method array getColor() Gets options of color proccessing in the MySheet
 * @method this setPlugins(array $colorOptions) Sets the priority of plugins 
 * @method array getPlugins() Gets the priority of plugins
 * @method this setCssRenderer(array $rendererOptions) Sets the style of the output CSS
 * @method array getCssRenderer() Gets the style of the output CSS
 * @method this setParser(string $parser) Sets the class of the parser (must be a realization of IParser)
 * @method \MSSLib\Tools\IParser getParser() Gets the class of the parser
 * @method this setImport(array $importOptions) Sets the options of importing MSS files
 * @method array getImport() Gets the options of importing MSS files
 * @method this setLanguage(string $lang) Sets the language that is used in MySheet
 * @method string getLanguage() Gets the language that is used in MySheet
 */
class MSSettings
{
    use MagicPropsTrait, 
        MagicMethodsTrait;
    
    public $color;
    public $plugins;
    public $cssRenderer;
    public $parser;
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
