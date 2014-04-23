<?php

define('EXT', '.php');
require_once 'lib/MySheet' . EXT;

use MySheet\MySheet;

$mysheet = new MySheet();

$code = <<<TEXT
h1 
    color black
    font-size 10px
    sort abc
        
body
    padding 0
    margin 0
        
    .title
        font-size 24px
        color red
        
        .colored
            color purple
            .colorsd
                
        
TEXT;
        

$result = $mysheet->parseCode($code);

var_dump($result);