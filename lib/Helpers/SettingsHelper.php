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

namespace MSSLib\Helpers;

/**
 * Description of SettingsHelper
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class SettingsHelper {
    
    public static function createObjectWithSettings($classname, array $settings = []) {
        $instance = new $classname();
        foreach ($settings as $name => $value) {
            $instance->$name = $value;
        }
        return $instance;
    }
    
    public static function createObjectFromSettings($settings) {
        if (!isset($settings['class'])) {
            return null;
        }
        $classname = $settings['class'];
        $settings = ArrayHelper::filter($settings, function ($key, $value) {
            if ($key !== 'class')
                return true;
            return false;
        });
        return self::createObjectWithSettings($classname, $settings);
    }
    
    
}
