<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Testing\PHPUnit\Tests;

use MySheet\Testing\PHPUnit;
use MySheet\MySheet;

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
//        var_dump($tests);
        return $tests;
    }
    
    
}