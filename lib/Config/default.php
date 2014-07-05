<?php
use MySheet as MSN;
use MySheet\MySheet;

return [
    'paramPriority' => 'commaSequence string variable color metric',
    'color' => [
        'lib' => [
            'class' => '\MySheet\Functionals\ColorLib\MrColorLib',
            'libPath' => '../../php_libs/mrcolor'
        ],
        'defaultType' => 'hex',
        'transform' => 'all'
    ],
    'plugins' => [
        'mixin'
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