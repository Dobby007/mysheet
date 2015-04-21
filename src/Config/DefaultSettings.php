<?php
use MSSLib as MSN;
use MSSLib\MySheet;

return [
    'mssClasses' => 'sequence mathExpr string variable color function metric nonQuotedString',
    'parserExtensions' => 'import media variable atRule ruleset',
    'functionModules' => 'metric',
    'color' => [
        'lib' => [
            'class' => '\MSSLib\Essentials\ColorLib\MrColorLib',
//            'libPath' => '../../php_libs/mrcolor-0.0.1'
        ],
        'transform' => 'all',
        'defaultTypeAlpha' => 'rgba',
        'defaultType' => 'hex',
        'allowHtmlColorOutput' => false
    ],
    'dependencies' => [
    ],
    'plugins' => [
        'mixin' => [
            'mixinSets' => ['basic']
        ], 
        'selectorExtensions'
    ],
    'cssRenderer' => [
//        'prefixRule' => '',
//        'suffixRule' => ' /* this is a CSS rule */',
//        'sepSelectors' => ', ',
//        'sepRules' => '; ',
//        'prefixCCB' => '',
//        'suffixCCB' => ''
    ],
    'system' => [
        'internal_autoload' => false
    ],
    'import' => [
        'paths' => [MySheet::WORKDIR . MSN\DS . 'Etc' . MSN\DS . 'MssTemplates'],
        'includeError' => 'exception'
    ],
    'dataUrl' => [
        'autoConvert' => true,
        'sizeLimit' => 20
    ],
    'urlFunction' => [
        'autoPrefix' => false
    ],
    'parser' => '\MSSLib\Essentials\BlockParser',
    'language' => 'en_us'
];