<?php

return [
    'paramPriority' => 'variable color metric',
    'color' => [
        'lib' => 'mrcolor',
        'libPath' => '../../../php_libs/mrcolor',
        'defaultType' => 'rgb',
        'transform' => 'unknown'
    ],
    'plugins' => [
        'mixin'
    ],
    'cssRenderer' => [
        'lfAfterSelector' => false,
        'lfAfterRule' => true,
        'prefixRule' => '    ',
        'lfBeforeOCB' => true,
        'lfBeforeCCB' => true,
        'sepSelectors' => ', '
    ],
    'parser' => 'MySheet\Tools\Parser'
];