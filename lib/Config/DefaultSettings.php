<?php
use MSSLib as MSN;
use MSSLib\MySheet;

return [
    'ruleParams' => 'commaSequence string variable color function metric other',
    'parserExtensions' => 'import media variable ruleset',
    'color' => [
        'lib' => [
            'class' => '\MSSLib\Essentials\ColorLib\MrColorLib',
//            'libPath' => '../../php_libs/mrcolor-0.0.1'
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
        'paths' => [MySheet::WORKDIR . MSN\DS . 'Etc' . MSN\DS . 'MssTemplates'],
        'includeError' => 'exception'
    ],
    'parser' => '\MSSLib\Tools\BlockParser',
    'language' => 'ru_ru'
];