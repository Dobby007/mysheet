<pre>
<?php

define('EXT', '.php');
require_once 'lib/MySheet' . EXT;

use MySheet\MySheet;

$mysheet = new MySheet();

//@mixin border-radius
//  

$code = <<<TEXT

h1 , h2, h3,h4
    color black
    font-size 10px
    .sort abc
        color sdfsd
        .underline
            text-decoration underline
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
        
TEXT;
        

$result = $mysheet->parseCode($code);

var_dump($result);

echo "\n\n:::COMPILED:::\n\n" . $result->toRealCss();

?>
</pre>