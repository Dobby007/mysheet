<?php
use MySheet as MSN;
use MySheet\MySheet;

return [
    'ruleParams' => 'commaSequence string variable color metric other',
    'parserExtensions' => 'import media variable ruleset',
    'color' => [
        'lib' => [
            'class' => '\MySheet\Functionals\ColorLib\MrColorLib',
            'libPath' => '../../php_libs/mrcolor'
        ],
        'defaultType' => 'hex',
        'transform' => 'all'
    ],
    'plugins' => [
        'mixin', 'selectorExtensions'
    ],
    'cssRenderer' => [
//        'prefixRule' => '',
//        'sepSelectors' => ', ',
//        'sepRules' => '; ',
//        'prefixCCB' => '',
//        'suffixCCB' => ''
    ],
    'import' => [
        'paths' => [MySheet::WORKDIR . DS . 'MssTemplates'],
        'includeError' => 'exception'
    ],
    'parser' => 'MySheet\Tools\Parser'
];