<?php
use MSSLib as MSN;
use MSSLib\MySheet;

return [
    'mssClasses' => 'sequence mathExpr string variable color function metric nonQuotedString',
    'parserExtensions' => 'import media variable ruleset',
    'functionModules' => 'metric',
    'color' => [
        'lib' => [
            'class' => '\MSSLib\Essentials\ColorLib\MrColorLib',
//            'libPath' => '../../php_libs/mrcolor-0.0.1'
        ],
        'transform' => 'all'
    ],
    'dependencies' => [
        'treeLib' => '../../php_libs/nicmart-tree/manual-init.php'
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
    'system' => [
        'internal_autoload' => false
    ],
    'import' => [
        'paths' => [MySheet::WORKDIR . MSN\DS . 'Etc' . MSN\DS . 'MssTemplates'],
        'includeError' => 'exception'
    ],
    'parser' => '\MSSLib\Tools\BlockParser',
    'language' => 'ru_ru'
];