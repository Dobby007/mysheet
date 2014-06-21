<pre>
<?php

define('EXT', '.php');
require_once 'lib/MySheet' . EXT;

use MySheet\MySheet;

$mysheet = MySheet::Instance();

//@mixin border-radius
//  

$code = <<<TEXT

           
@mixin border-radius (left right )
    -webkit-border-radius \$left \$right
    -moz-border-radius 3px
    border-radius \$arguments \$left \$right \$left \$right
    

h1 , h2, h3,h4
    color rgb(1,.3003px,   3 )
    background-color #000000
    font-size 10px
    .sort 
        color sdfsd
        .underline
            text-decoration underline ;; ;
            border-bottom :    1px           solid     gray  ;
body
    padding 0
    margin 0
        
    .title 
        font-size 24px
        color red
        .colored
        h1 as
        .grayed
            color purple
            .colorsd
                width 300px
                border-radius 4px 5px 6px 7px 8px 9px
    
    

 
        
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