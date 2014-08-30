<meta charset="utf-8">
<div style="font-family: 'Batang', Arial , 'DejaVu Sans'">Съешь еще этих мягких французских булочек и выпей чаю</div>

<pre>
<?php

function register_other1() {
    echo "\nreg1";
    spl_autoload_register(function ($className) {
        echo "\nCan not load class with other autoload 1: " . $className;
    });
}

function register_other2() {
    echo "\nreg2";
    spl_autoload_register(function ($className) {
        echo "\nCan not load class with other autoload 2: " . $className;
    });
}

function register_other3() {
    echo "\nreg3";
    spl_autoload_register(function ($className) {
        echo "\nCan not load class with other autoload 3: " . $className;
    });
}




//define('EXT', '.php');
require_once 'lib/MySheet.php';

use MSSLib\MySheet;
use MSSLib\Tools\MSSettings;

//MySheet::setDebugMode(true);

$mysheet = MySheet::Instance();

$mysheet->getAutoload()->registerAutoload();
$settings = new MSSettings();
$settings->setCssRenderer([
    'prefixOCB' => "\n{"
]);
//$settings->set('color.lib.libPath', 'sss');
$mysheet->getAutoload()->restoreAutoload();
$mysheet->init($settings);


$code = <<<TEXT
@import "somefile.mss" aural, screen, tv #090
@import "main.mss"
   
@mixin border-radius (left right )
    -webkit-border-radius \$left \$right
    -moz-border-radius 3px
    border-radius \$arguments \$left \$right \$left \$right
    
@media screen and width >= 1024px {

    h1 , h2, h3,h4
        color rgb(220,120, 30 )
        border-color #f3f
        border-bottom-color hsl(300, 80%, 80%)
        background-color red
        font-size 10px
        .sort 
            color sdfsd
            .underline
                text-decoration underline ;
                border-bottom :    1px           solid     gray  ;
    
    body:any(:hover, :active) {
        padding 0
        margin 0
        gradient as
        \$varname = mememe
        html.ie6 & .title
            font-size 24px
            color red
            .colored
            h1 as
            {
                sanya: 999; aa: #33
                sort asc
                    take a challenge
                    and win
            }
            .grayed
            {
                color purple
                .colorsd
                    width 300px
                    border-radius 4px 5px 6px 7px 8px 9px
            }
    }
}

 
        
TEXT;
        
$code2 = <<<TEXT
html { color false; h1: true; hi: #333}
body {   
    rule: red
    .wrapper
        decl value
        set h1
            color high-definition
    
}

.header
    border thick

TEXT;

$code3 = <<<TEXT
h1 span, h1 i
    color red
    font-style italic

h2 {
    color rgb(220,120, 30 )
}
        
h3 
{
    color: red
}

h4
    color: red;
    
h5 { color: red; background-color: green; }

TEXT;

$code4 = <<<TEXT
body:any(:hover, :active) {
    padding 0
    margin 0
    gradient as
    
    html.ie6 & .title
        font-size 24px
        color red
        .colored
        h1 as { sanya: 999; aa: #33
            sort asc
                take a challenge
        }
TEXT;

$code5 = <<<TEXT
h1 {color: red;} h2 {border: 1px solid green} h3 {text-align:right }
span
    tutu tt
TEXT;

$result = $mysheet->parseCode($code);

//var_dump($result);

echo "\n\n:::COMPILED:::\n\n" . $result->toRealCss();

?>
</pre>