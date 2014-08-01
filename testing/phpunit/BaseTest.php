<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Testing\PHPUnit\Tests;

use MySheet\Testing\PHPUnit as PU;

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
