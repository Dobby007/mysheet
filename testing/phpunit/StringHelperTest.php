<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Testing\PHPUnit\Tests;

use MySheet\Testing\PHPUnit;
use MySheet\MySheet;
use MySheet\Helpers\StringHelper;
use MySheet\Helpers\ArrayHelper;

/**
 * Description of simple
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class StringHelperTest extends BaseTest
{
    protected $mysheet;

    public static function setUpBeforeClass()
    {
        MySheet::Instance()->getAutoload()->registerAutoload();
    }
    
    public static function tearDownAfterClass()
    {
        MySheet::Instance()->getAutoload()->restoreAutoload();
    }
    
    /**
     * @dataProvider dataSplittedListParsing
     */
    public function testSplittedListParsing($string, $delimiter, $expected)
    {
        $result = StringHelper::parseSplittedString($string, $delimiter);
        $this->assertEquals($expected, $result);
    }
    
    /**
     * @dataProvider dataFunctionParsing
     */
    public function testFunctionParsing($string, $spaceInArgs, $expected)
    {
        $parsed = StringHelper::parseFunction($string, $spaceInArgs);
        $result = [];
        ArrayHelper::concat($result, $parsed['name'], $parsed['arguments']);
        $this->assertEquals($expected, $result);
    }
    
    
    public function dataFunctionParsing() {
        return [
            ['rgb(0, 120, 255)', false, ['rgb', '0', '120', '255']],
            ['rgba   (10% , 20%, 30%)', false, ['rgba', '10%', '20%', '30%']],
            [':not(.selected)', false, [':not', '.selected']],
            [':not(.selected .hover)', true, [':not', '.selected .hover']],
        ];
    }
    
    public function dataSplittedListParsing() {
        return [
            ['abc, 123, 0, 0%', ',', ['abc', '123', '0', '0%']],
            ['abc   , 123, 0!    ,   0%', ',', ['abc', '123', '0!', '0%']],
            ['1px   , 0%', ',', ['1px', '0%']],
            ['1px   , \'0%\'    ', ',', ['1px', '\'0%\'']],
            ['1px   , 0%    ', ',', ['1px', '0%']],
            ['1px/0.5    ', '/', ['1px', '0.5']],
            ['1px', ',', ['1px']],
            ['1|0', '|', ['1', '0']],
            ['"1px,"   , 0%    ', ',', ['"1px,"', '0%']],
            ['(1, 0, 2), 0% ', ',', ['(1, 0, 2)', '0%']],
        ];
    }
    
}