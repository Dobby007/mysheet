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
class FunctionalTest extends BaseTest
{
    protected $mysheet;

    protected function setUp()
    {
        $this->mysheet = MySheet::Instance();
        MySheet::Instance()->init();
    }
    
    /**
     * @dataProvider dataMultipleSelectors
     */
    public function testMultipleSelectors($mssSource, $expectedCss)
    {
        $doc = $this->mysheet->parseCode($mssSource);
        $this->assertEquals( $expectedCss, trim($doc->toRealCss()) );
    }
    
    public function dataMultipleSelectors() {
        $tests = self::getAvailableTests('FunctionalTest', 'MultipleSelectors');
        $tests = array_map(function ($filePath) {
            $test = self::getTest($filePath);
            return [
                $test['source'],
                $test['expected']
            ];
        }, $tests);
        return $tests;
    }
    
    /**
     * @dataProvider dataFreeSyntax
     */
    public function testFreeSyntax($mssSource, $expectedCss)
    {
        $doc = $this->mysheet->parseCode($mssSource);
        $this->assertEquals( $expectedCss, trim($doc->toRealCss()) );
    }
    
    public function dataFreeSyntax() {
        $tests = self::getAvailableTests('FunctionalTest', 'FreeSyntax');
        $tests = array_map(function ($filePath) {
            $test = self::getTest($filePath);
            return [
                $test['source'],
                $test['expected']
            ];
        }, $tests);
        return $tests;
    }
    
    /**
     * @dataProvider dataColorManipulation
     */
    public function testColorManipulation($mssSource, $expectedCss)
    {
        $doc = $this->mysheet->parseCode($mssSource);
        $this->assertEquals( $expectedCss, trim($doc->toRealCss()) );
    }
    
    public function dataColorManipulation() {
        $tests = array_map(function ($filePath) {
            $test = self::getTest($filePath);
            return [
                $test['source'],
                $test['expected']
            ];
        }, self::getAvailableTests('FunctionalTest', 'ColorManipulation'));
        return $tests;
    }
    
    /**
     * @dataProvider dataComplexFunctions
     */
    public function testComplexFunctions($mssSource, $expectedCss)
    {
        $doc = $this->mysheet->parseCode($mssSource);
        $this->assertEquals( $expectedCss, trim($doc->toRealCss()) );
    }
    
    public function dataComplexFunctions() {
        $tests = array_map(function ($filePath) {
            $test = self::getTest($filePath);
            return [
                $test['source'],
                $test['expected']
            ];
        }, self::getAvailableTests('FunctionalTest', 'ComplexFunctions'));
        return $tests;
    }
    
    /**
     * @dataProvider dataMixins
     */
    public function testMixins($mssSource, $expectedCss)
    {
        $doc = $this->mysheet->parseCode($mssSource);
        $this->assertEquals( $expectedCss, trim($doc->toRealCss()) );
    }
    
    public function dataMixins() {
        $tests = array_map(function ($filePath) {
            $test = self::getTest($filePath);
            return [
                $test['source'],
                $test['expected']
            ];
        }, self::getAvailableTests('FunctionalTest', 'Mixins'));
        return $tests;
    }
    
}