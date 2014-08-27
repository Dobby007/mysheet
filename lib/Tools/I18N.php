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

/**
 * Description of I18N
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class I18N {
    protected static $language;
    protected static $files = array();
    
    private function __construct() { }
    
    protected static function get18Name($category, $language) {
        return $category . '.' . $language;
    }
    
    protected static function load18Strings($category, $language) {
        $lang_file = \MSSLib\MySheet::WORKDIR . DIRECTORY_SEPARATOR . 'Etc' . DIRECTORY_SEPARATOR . 'I18N' . DIRECTORY_SEPARATOR . $category . DIRECTORY_SEPARATOR . $language . '.ini';
        self::$files[self::get18Name($category, $language)] = file_exists($lang_file) ? parse_ini_file($lang_file, true) : [];
    }
    
    protected static function getCatInfo($category, &$mainCat, &$subCategory) {
        $pos = strpos($category, '.');
        if ($pos !== false) {
            $mainCat = substr($category, 0, $pos);
            $subCategory = substr($category, $pos + 1);
        } else {
            $mainCat = $category;
            $subCategory = null;
        }
    }
    
    public static function setLanguage($lang) {
        self::$language = $lang;
    }
    
    public static function getLanguage() {
        return self::$language;
    }
    
    public static function getPhrase($category, $code) {
        self::getCatInfo($category, $mainCat, $subCat);
        self::load18Strings($mainCat, self::getLanguage());
        $name = self::get18Name($mainCat, self::getLanguage());
        $phrase = null;
        if ($subCat !== null) {
            if (isset(self::$files[$name][$subCat][$code])) {
                $phrase =  self::$files[$name][$subCat][$code];
            }
        } else  {
            if (isset(self::$files[$name][$code])) {
                $phrase =  self::$files[$name][$code];
            }
        }
        return $phrase;
    }
    
    public static function translate($category, $code, $arguments = array()) {
        $phrase = self::getPhrase($category, $code);
        $phrase = preg_replace_callback('/\$([0-9]+)/i', function ($matches) use ($arguments) {
            $id = $matches[1] - 1;
            return isset($arguments[$id]) ? $arguments[$id] : '';
        }, $phrase);
        return $phrase;
    }
}
