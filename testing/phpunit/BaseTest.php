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

namespace MSSLib\Testing\PHPUnit\Tests;

use MSSLib\Testing\PHPUnit as PU;

/**
 * Description of BaseTest
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
abstract class BaseTest extends \PHPUnit_Framework_TestCase {
    public static function getAvailableTests($testClass, $testSubClass) {
        $path = PU\WORKDIR . PU\DS . 'Data' . PU\DS . $testClass . PU\DS . $testSubClass . PU\DS;
        $files = [];
        if (is_dir($path)) {
            foreach (glob($path . '*.mss') as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
                var_dump(strrpos($file, '.'));
                $files[] = substr($file, 0, strrpos($file, '.'));
            }
            var_dump($files);
        }
        
        return $files; 
    }
    
    public static function getTest($filePath) {
        $content_test = file_get_contents($filePath . '.mss');
        $content_result = file_get_contents($filePath . '.css');
        return [
            'source' => trim($content_test),
            'expected' => trim($content_result)
        ];
    }
}
