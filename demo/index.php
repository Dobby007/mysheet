<meta charset="utf-8">
<div style="font-family: 'Batang', Arial , 'DejaVu Sans'">Съешь еще этих мягких французских булочек и выпей чаю</div>

<pre>
<?php
set_time_limit(4);
require_once '../src/MySheet.php';

use MSSLib\MySheet;
use MSSLib\Essentials\MSSettings;

MySheet::setDebugMode(true);



$mysheet = MySheet::Instance();
$mysheet->setActiveDirectory(realpath('./'));
$mysheet->getAutoload()->registerAutoload();
$settings = new MSSettings();
$settings->setDependencies(['../../php_libs/nicmart-tree/manual-init.php']);
$settings->set('color.lib.libPath', '../../mrcolor');
$mysheet->init($settings);


$code1 = <<<TEXT
@import "somefile.mss" aural, screen, tv #090
@import "main.mss"
   
/* My simple mixin 
        It just registeres new mixin
    */
        
@mixin border-radius (left right )
    -webkit-border-radius \$left \$right
    -moz-border-radius \$arguments
    border-radius \$arguments \$left \$right \$left \$right
    /* new comment*/
@media screen and width >= 1024px {
    h1 , h2, h3,h4
        ~color rgb(220,120, 30 ) /// this property is no longer usable
        border-color #f3f
        border-bottom-color hsla(350, 80%, 80%, 1)
        background-color red
        font-size 36px
        .sort 
            color sdfsd
            .underline
                text-decoration underline ;
                border-bottom :    1px           solid     gray  ;
                border-radius 4px
    
    body:any(:hover, :active) {
        padding 0
        margin 0
        gradient as
        \$varname = 5px + 50%
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

.wrapper
    font-size 23px
   
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
    width 5px + (50px + 3%) * 4px
    max-width 5px*4-(4*3)+2
    height unitless((4px/70%))+ unitless(abs(negate(2px - 5px)))
    image url(my/image.png)
    font italic/glamour -1
    expression (4+3
TEXT;

$code9 = <<<TEXT
@import url(http://fonts.googleapis.com/css?family=Open+Sans&subset=latin,cyrillic);
@import "site-common.mss"
        
html
    height 100%
    body
        line-height 105%
        color #777
        height 100%
        font-family: 'Open Sans', sans-serif;
        .wrapper
            position relative
            min-height 100%
            #header
                color #fff
                background-color rgba(0, 0, 0, 60%)
                #logo
                    float left
                    .title
                        padding 4px 5px
                        font-weight bold
                        font-size 14pt
                        a:hover
                            border 0
                            color:#09f;
                #main-menu
                    overflow hidden
                    ul
                        float right
                        li
                            float left
                            padding 8px 6px
                            a
                                color #fff
            .main-presentation
                padding 10px
                background-color rgb(62, 161, 218)
                color white
                .version-note
                    letter-spacing 2.1px
                    text-transform uppercase
                    font-family avenir,helvetica,arial,sans-serif
                    margin 15px 0
                .shaddowed
                    text-shadow 3px 3px 10px
            #pcontent
                position relative
                .content-sidebar
                    position absolute
                    top 0
                    height 100%
                    width 20%
                    background-color rgb(62, 161, 180)
                    border-right 10px solid rgb(62, 161, 220)
                    color white
                    overflow auto
                    .navigation
                        padding-top 10px
                        li 
                            padding 0
                            margin 0
                            li a
                                padding-left 20pt
                            a
                                padding 6px 20pt 6px 5pt
                                display block
                            a:hover
                                border 0
                                background-color rgb (62, 161, 100)
                .text-content
                    padding: 10px 100px
                    a
                        color blue
                &.with-sidebar
                    position absolute
                    top 32px
                    bottom 0
                    left 0
                    width: 100%
                    .text-content
                        position absolute
                        left 20%
                        margin-left 10px
                        right 0
                        top 0
                        bottom 0
                        overflow auto
            .footer_clear
                height 200px

            #footer
                display block
                position absolute
                bottom 0
                left 0
                width 100%
                background-color rgb(62, 161, 218)
                color white
                padding 5px 0
                .copyright-notice
                    color white
                    text-align center
                .yii-notice 
                    text-align center
                #footer-menu
                    margin-top 10px
                    ul
                        text-align center
                        li
                            display inline
                            margin-left 4px
                        li:after
                            margin-left 4px
                            content '·'
TEXT;

$code10 = <<<TEXT
header
    color red + 50lt - 40sat
    color rgba(10,10,10,.7)
    border-bottom-color hsla(28, 76%, 49%, 0.7)
    color red
    color #000 + 20r + 20b - 255g + 240g
article
    color #acd - 20lt
body
    color #000 + asdas255b + 40g
TEXT;

$code11 = <<<TEXT
\$percentage = 0.01 * 100
.body
   filter: progid:DXImageTransform.Microsoft.Alpha(opacity = \$percentage)
   filter progid:DXImageTransform.Microsoft.Alpha(\$percentage, \$percentage * 2, \$percentage * 3)
   filter progid:DXImageTransform.Microsoft.gradient(startColorstr='#917c4d', endColorstr='#ffffff')
   background: -khtml-url(asd) -webkit-url(dodo) -moz-url(tata) -ms-url(taktak)
TEXT;

$code12 = <<<TEXT
selector
    width 5px + (50px + 3%) * 4px
    max-width 5px*4-(4*3)+2
    height unitless((4px/70%))+ unitless(abs(negate(2px - 5px)))
    image url(my/image.png)
    font italic/glamour -1
    expression (4+3)
.testimage
    width 80px
    height 80px !important !prefixWith(ms, moz)
    border-radius 4px
    background-image url(examples/images/logo.png)
    background url(examples/images/testimage.png)
    ~background-image url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAQ5JREFUeNpi/P//PwMlgImBQjDwBrCgCxj1XGfg4OZmYGNnj2FgZCxg+P9/wq+fP5f8+PqV4VyJJnEuAAZsDFBTQZS7mDGIBvGJ9gJI8c9v3wri/OWMX/xgYIj2kzMG8XEZgmHAz+/fbb9/+cIwcdbps4+/MzBMmX36LIgPEicqDP7/+5f+++dPht+/fp55+JWB4dvnTwysbOwmrOzsxAXi148fGUA2gsDrn0ADPn0GsoD4zjYgbYo1wFAw2FRxLQbuyCVndA7+/w+iQXxsakGYBZuz/ry8pvH/8YVbN/q+Mfx/e+vW35fXjIDC14D4B7paRvS8wMjICKJEgJgN2aEgHwHV/iFowNDLCwABBgC9qJ54WqC2JwAAAABJRU5ErkJggg==)
TEXT;

$code13 = <<<TEXT
html
    height 0
    width 50px !prefixWith(ms, moz) !important
    border-radius 5px !important
    filter-grayscale 50%
    transform scale(2)
TEXT;

$result = null;
try {
    $result = $mysheet->parseCode($code12);
//    $result = $mysheet->parseFile(realpath('examples/bootstrap/bootstrap.css'));
//    $result = $mysheet->parseFile(realpath('examples/exm3/exam3.mss'));
    //$result = $mysheet->parseFicle(__DIR__ . '/examples/main.mss');


    $resultRulesets = \MSSLib\Tools\Finders\RulesetFinder::querySelectorAll('.wrapper, .header', $result);

    foreach ($resultRulesets as $resultRuleset) {
        var_dump($resultRuleset ? $resultRuleset->toRealCss() : 'not found!');
    }


    //var_dump($result);


    $compiledCode = $result->toRealCss();
    echo "\n\n:::COMPILED:::\n\n";
    echo $compiledCode;
} catch (\MSSLib\Error\MySheetException $ex) {
    echo "\n" . $ex . "\n";
    echo($ex->getTraceAsString());
//    throw $ex;
}
?>
</pre>
<style>
    <?php // echo $compiledCode; ?>
    
</style>


<div class="testimage"></div>