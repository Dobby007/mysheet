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


interface IMSSettings
{
    /**
     * Loads settings from array
     * @param array $settings
     */
    function load(array $settings);
    
    /**
     * Gets the value of the specific option or some default value if that option was not found.
     * Option can be identified by its' path.
     * Examples of paths: color.lib.libPath, color, cssRenderer.prefixOCB
     * @param string $complex_name Path to the option
     * @param mixed $default Value to be returned if the option is not found.
     * @return $this
     */
    public function get($complex_name, $default = null);
    
    /**
     * Sets the value to the specific option. Option can be identified by its' path.
     * Examples of paths: color.lib.libPath, color, cssRenderer.prefixOCB
     * @param string $complex_name Path to the option
     * @param mixed $value Value
     * @return $this
     */
    function set($complex_name, $value);
    
    
    
    
}
