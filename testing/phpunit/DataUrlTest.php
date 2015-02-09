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

use MSSLib\Testing\PHPUnit;
use MSSLib\MySheet;

/**
 * Description of simple
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class DataUrlTest extends BaseTest
{
    protected $mysheet;
    protected static $dir;
    protected static $testFilesDir;

    
    public function setUp() {
        $this->mysheet = MySheet::Instance();
        MySheet::Instance()->init();
    }
    
    
    /**
     * @dataProvider dataFileEncode
     */
    public function testFileEncode($filepath, $result)
    {
        $resultObject = \MSSLib\EmbeddedClasses\DataUrlClass::fromFile($filepath);
        $this->assertEquals( $result, trim($resultObject->toRealCss(new \MSSLib\Essentials\VariableScope())) );
    }
    
    public function dataFileEncode() {
        $testFilesDir = __DIR__ . DIRECTORY_SEPARATOR . 'Data' . DIRECTORY_SEPARATOR . 'UnitTest' . DIRECTORY_SEPARATOR . 'DataUrl';
        $files = ['color-wheel.png', 'geometry.png'];
        $tests = array_map(function ($file) use ($testFilesDir) {
            $testFile = $testFilesDir. DIRECTORY_SEPARATOR . $file;
            $expResFile = $testFilesDir . DIRECTORY_SEPARATOR . $file . '.txt';
            return [
                $testFile,
                file_get_contents($expResFile)
            ];
        }, $files);
        return $tests;
    }
    
    
}