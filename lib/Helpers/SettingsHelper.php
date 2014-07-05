<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Helpers;

/**
 * Description of SettingsHelper
 *
 * @author dobby007
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
