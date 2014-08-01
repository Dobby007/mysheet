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
    
@media screen and width >= 1024px

    h1 , h2, h3,h4
        color rgb(220,120, 30 )
        border-color #f3f
        border-bottom-color hsl(350, 80%, 80%)
        background-color red
        font-size 10px
        .sort 
            color sdfsd
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


$result = $mysheet->parseCode($code);

//var_dump($result);

echo "\n\n:::COMPILED:::\n\n" . $result->toRealCss();

?>
</pre>