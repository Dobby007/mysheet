<meta charset="utf-8">
<div style="font-family: 'Batang', Arial , 'DejaVu Sans'">Съешь еще этих мягких французских булочек и выпей чаю</div>

<pre>
<?php

//define('EXT', '.php');
require_once 'lib/MySheet.php';

use MySheet\MySheet;

$mysheet = MySheet::Instance();

//@mixin border-radius
//  

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
                text-decoration underline ;; ;
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
    \$varname = mememe
    html.ie6 & .title
        font-size 24px
        color red
        .colored
        h1 as { sanya: 999; aa: #33
            sort asc
                take a challenge
        }
TEXT;


$result = $mysheet->parseCode($code3);

var_dump($result);

echo "\n\n:::COMPILED:::\n\n" . $result->toRealCss();

?>
</pre>