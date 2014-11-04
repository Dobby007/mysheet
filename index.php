<meta charset="utf-8">
<div style="font-family: 'Batang', Arial , 'DejaVu Sans'">Съешь еще этих мягких французских булочек и выпей чаю</div>

<pre>
<?php
require_once 'src/MySheet.php';

use MSSLib\MySheet;
use MSSLib\Tools\MSSettings;

MySheet::setDebugMode(true);

$mysheet = MySheet::Instance();

$mysheet->getAutoload()->registerAutoload();
$settings = new MSSettings();
$settings->setCssRenderer([
    'prefixOCB' => "\n"
]);
$settings->setCssRenderer([
    'prefixCCB' => "\n",
    'prefixOCB' => ""
]);
$settings->setSystem([
    'internal_autoload' => true
]);
$settings->set('color.lib.libPath', '../../mrcolor');

$mysheet->getAutoload()->restoreAutoload();
$mysheet->init($settings);


$code1 = <<<TEXT
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
        border-bottom-color hsla(350, 80%, 80%, 1)
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
    color: blue;
    
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

$code6 = <<<TEXT
.title,a{color:#B24926}#content a:hover{color:#333;}#home-content{margin-bottom:15px; }@media only screen and (-webkit-device-pixel-ratio:2){#content li.try-jquery a{background-image:url(i/try-jquery@2x.jpg)}}#banner-secondary a,#banner-secondary a:hover{color:#b3b3b3}#banner-secondary h3{color:#7ACEF4;margin-bottom:0.5em}#banner-secondary .features-box p{font-size:.8em;line-height:1em;padding:0}.feature-box-image{margin:0 auto 1em;width:120px;height:107px;overflow:hidden}.lightweight-footprint .feature-box-image{background:url(i/feature-sprites.png) 0 0 no-repeat}.css3-compliant .feature-box-image{background:url(i/feature-sprites.png) -139px 0 no-repeat}.cross-browser .feature-box-image{background:url(i/feature-sprites.png) -278px 0 no-repeat;-webkit-transition:all .4s;transition:all .4s;position:relative;z-index:10}.cross-browser .feature-box-image:hover{-webkit-transition:all .7s linear;-webkit-transform:rotate(6.28rad);transition:all .7s;transform:rotate(6.28rad)}#content #banner-secondary .downloads-box p{margin:15px 0 5px}.download-help,.download-options{margin-top:1em}#banner-secondary .download-options a{display:block}#banner-secondary .download-main .button{float:none;box-shadow:0 0 20px rgba(209,143,79,.6);-webkit-box-shadow:0 0 20px rgba(209,143,79,.6);border-radius:0;border:none;-webkit-transition:-webkit-box-shadow 1s linear;transition:all 1s}#banner-secondary .download-main .button:hover{box-shadow:0 0 25px rgba(209,143,79,.9);-webkit-box-shadow:0 0 25px rgba(209,143,79,.9)}#content #banner-secondary .download-main>a{display:block;padding:0 0 .5em;font-size:2em;width:100%;text-align:center;text-decoration:none;line-height:.8em}#content #banner-secondary .download-main>a span{display:block;font-size:.6em}#content #banner-secondary .download-main>a span.download{background:url(i/download.png);width:44px;height:37px;position:relative;top:1em;display:inline-block;margin-right:.5em}.resources{padding-left:60px}#content li.try-jquery{background:0 0;margin:10px 0 0;padding:0}#content li.try-jquery a{background:url(i/try-jquery.jpg) top left no-repeat;background-size:100%;height:126px;width:277px;text-indent:-9999px;display:block;max-width:100%}#content #corporate-members li{background:0 0;height:150px}#corporate-members span{display:inline-block;height:100%;vertical-align:middle}#corporate-members a{height:100%}#corporate-members img{vertical-align:middle;max-height:150px}#content .project-tiles{margin:0}.project-tile{padding-top:1em;padding-bottom:1em;text-align:center}.project-tile a{text-indent:-9999px;display:inline-block}#support-matrix{width:100%;margin:0 auto 1em}#support-matrix tbody tr{border-bottom:none}#support-matrix tbody tr:hover{background-color:transparent}#support-matrix td{text-align:center}
TEXT;

$code7 = <<<TEXT
@media only screen and (-webkit-device-pixel-ratio:2){#content li.try-jquery a{background-image:url(i/try-jquery@2x.jpg)}}.title,a{color:#B24926}#content a:hover{color:#333}#home-content{margin-bottom:15px}#banner-secondary a,#banner-secondary a:hover{color:#b3b3b3}#banner-secondary h3{color:#7ACEF4;margin-bottom:.5em}#banner-secondary .features-box p{font-size:.8em;line-height:1em;padding:0}.feature-box-image{margin:0 auto 1em;width:120px;height:107px;overflow:hidden}.lightweight-footprint .feature-box-image{background:url(i/feature-sprites.png) 0 0 no-repeat}.css3-compliant .feature-box-image{background:url(i/feature-sprites.png) -139px 0 no-repeat}.cross-browser .feature-box-image{background:url(i/feature-sprites.png) -278px 0 no-repeat;-webkit-transition:all .4s;transition:all .4s;position:relative;z-index:10}.cross-browser .feature-box-image:hover{-webkit-transition:all .7s linear;-webkit-transform:rotate(6.28rad);transition:all .7s;transform:rotate(6.28rad)}#content #banner-secondary .downloads-box p{margin:15px 0 5px}.download-help,.download-options{margin-top:1em}#banner-secondary .download-options a{display:block}#banner-secondary .download-main .button{float:none;box-shadow:0 0 20px rgba(209,143,79,.6);-webkit-box-shadow:0 0 20px rgba(209,143,79,.6);border-radius:0;border:none;-webkit-transition:-webkit-box-shadow 1s linear;transition:all 1s}#banner-secondary .download-main .button:hover{box-shadow:0 0 25px rgba(209,143,79,.9);-webkit-box-shadow:0 0 25px rgba(209,143,79,.9)}#content #banner-secondary .download-main>a{display:block;padding:0 0 .5em;font-size:2em;width:100%;text-align:center;text-decoration:none;line-height:.8em}#content #banner-secondary .download-main>a span{display:block;font-size:.6em}#content #banner-secondary .download-main>a span.download{background:url(i/download.png);width:44px;height:37px;position:relative;top:1em;display:inline-block;margin-right:.5em}.resources{padding-left:60px}#content li.try-jquery{background:0 0;margin:10px 0 0;padding:0}#content li.try-jquery a{background:url(i/try-jquery.jpg) top left no-repeat;background-size:100%;height:126px;width:277px;text-indent:-9999px;display:block;max-width:100%}#content #corporate-members li{background:0 0;height:150px}#corporate-members span{display:inline-block;height:100%;vertical-align:middle}#corporate-members a{height:100%}#corporate-members img{vertical-align:middle;max-height:150px}#content .project-tiles{margin:0}.project-tile{padding-top:1em;padding-bottom:1em;text-align:center}.project-tile a{text-indent:-9999px;display:inline-block}#support-matrix{width:100%;margin:0 auto 1em}#support-matrix tbody tr{border-bottom:none}#support-matrix tbody tr:hover{background-color:transparent}#support-matrix td{text-align:center}
TEXT;

$code8 = <<<TEXT
selector
    width 5px + (50px - 3%) + 4px
TEXT;

$result = $mysheet->parseCode($code8);
//$result = $mysheet->parseFicle(__DIR__ . '/examples/main.mss');

//var_dump($result);

echo "\n\n:::COMPILED:::\n\n" . $result->toRealCss();

//var_dump($result);
?>
</pre>