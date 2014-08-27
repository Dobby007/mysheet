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
            foreach (scandir($path) as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
                $files[] = $path . $file;
            }
        }
        
        return $files; 
    }
    
    public static function getTest($filePath) {
        $content = file_get_contents($filePath);
        $splitted = preg_split('/[\r\n\s]+====[\r\n\s]+/', $content);
//        var_dump($splitted);
        return [
            'source' => trim($splitted[0]),
            'expected' => trim($splitted[1])
        ];
    }
}
