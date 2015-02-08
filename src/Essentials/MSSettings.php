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

namespace MSSLib\Essentials;

use MSSLib as MS;
use MSSLib\Traits\MagicPropsTrait;
use MSSLib\Traits\MagicMethodsTrait;
use MSSLib\Helpers\SettingsHelper;

/**
 * Description of MSSettings
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 * @property string[] mssClasses
 * @property string[] parserExtensions 
 * @method this setColor(array $colorOptions) Sets instructions for the lib on how to work with colors
 * @method array getColor() Gets options of color proccessing in the MySheet
 * @method this setSystem(array $colorOptions) Sets system settings
 * @method array getSystem() Gets system settings
 * @method this setPlugins(array $colorOptions) Sets the priority of plugins 
 * @method array getPlugins() Gets the priority of plugins
 * @method this setCssRenderer(array $rendererOptions) Sets the style of the output CSS
 * @method array getCssRenderer() Gets the style of the output CSS
 * @method this setParser(string $parser) Sets the class of the parser (must be a realization of IParser)
 * @method string getParser() Gets the class of the parser
 * @method this setImport(array $importOptions) Sets the options of importing MSS files
 * @method array getImport() Gets the options of importing MSS files
 * @method this setLanguage(string $lang) Sets the language that is used in MySheet
 * @method string getLanguage() Gets the language that is used in MySheet
 */
class MSSettings implements IMSSettings
{
    use MagicPropsTrait,
        MagicMethodsTrait;                                                                                                                                                               // :-)
    
    public $color;
    public $plugins;
    public $cssRenderer;
    public $parser;
    public $import;
    public $language;
    public $system;
    public $dependencies;
    public $cssData;
    public $urlFunction;
    private $_mssClasses;
    private $_parserExtensions;
    private $_functionModules;
    
    public function __construct(array $settings = []) {
        $this->load($settings);
    }
    
    /**
     * Loads settings from array
     * @param array $settings
     */
    public function load(array $settings) {
        $default = require(MS\WORKDIR . MS\DS . 'Config' . MS\DS . 'DefaultSettings' . MS\EXT);
        $settings = array_replace_recursive($default, $settings);

        foreach ($settings as $name => $value) {
            $this->$name = $value;
        }
    }
    
    /**
     * Gets the value of the specific option or some default value if that option was not found.
     * Option can be identified by its' path.
     * Examples of paths: color.lib.libPath, color, cssRenderer.prefixOCB
     * @param string $complex_name Path to the option
     * @param mixed $default Value to be returned if the option is not found.
     * @return $this
     */
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
    
    /**
     * Sets the value to the specific option. Option can be identified by its' path.
     * Examples of paths: color.lib.libPath, color, cssRenderer.prefixOCB
     * @param string $complex_name Path to the option
     * @param mixed $value Value
     * @return $this
     */
    public function set($complex_name, $value) {
        $complex_name = explode('.', $complex_name);
        $propName = array_shift($complex_name);
        $var = $this->$propName;
        $rootVar = &$var;
        foreach ($complex_name as $name) {
            if (is_array($var)) {
                $var[$name] = isset($var[$name]) ? $var[$name] : false;
            } else {
                $var = [
                    $name => []
                ];
            }
            
            if (isset($var[$name])) {
                $var = &$var[$name];
            }
        }
        $var = $value;
        $this->$propName = $rootVar;
        return $this;
    }
    
    /**
     * Method is called when user tries to set a value to a property through its' magic "get" and "set" methods.    
     * Method is overriden.
     * @param string $propName
     * @param mixed $propValue
     * @return boolean
     */
    protected function __magicSetProperty($propName, $propValue) {
        return $this->setValueToProperty($propName, $propValue);
    }
    
    /**
     * Sets value to a property. If the $propValue is array it tries to merge with the existing
     * @param string $propName
     * @param mixed $propValue
     * @return boolean
     */
    protected function setValueToProperty($propName, $propValue) {
        if (is_array($this->$propName) && is_array($propValue)) {
            $this->$propName = array_replace_recursive($this->$propName, $propValue);
        } else {
            $this->$propName = $propValue;
        }
        return true;
    }

    /**
     * Sets a priority for parsing of rule parameters
     * @param string|array $paramPriority
     * @return $this
     */
    public function setMssClasses($paramPriority) {
        $paramPriority = SettingsHelper::convertPriorityStringToArray($paramPriority);
        if (is_array($paramPriority)) {
            $this->_mssClasses = $paramPriority;
        }
        return $this;
    }
    
    /**
     * Gets a priority for parsing of rule parameters
     * @return array
     */
    public function getMssClasses() {
        return $this->_mssClasses;
    }
    
    /**
     * Sets a priority for parser extensions
     * @param string|array $paramPriority
     * @return $this
     */
    public function setParserExtensions($parserExtensions) {
        $parserExtensions = SettingsHelper::convertPriorityStringToArray($parserExtensions);
        if (is_array($parserExtensions)) {
            $this->_parserExtensions = $parserExtensions;
        }
        return $this;
    }
    
    /**
     * Gets a priority for parser extensions
     * @return array
     */
    public function getParserExtensions() {
        return $this->_parserExtensions;
    }
    
    /**
     * Gets a priority for functions' modules
     * @return array
     */
    function getFunctionModules() {
        return $this->_functionModules;
    }

        
    /**
     * Sets a priority for functions' modules
     * @param string|array $functionModules
     * @return $this
     */
    function setFunctionModules($functionModules) {
        $functionModules = SettingsHelper::convertPriorityStringToArray($functionModules);
        if (is_array($functionModules)) {
            $this->_functionModules = $functionModules;
        }   
        return $this;
    }

    
    
}
