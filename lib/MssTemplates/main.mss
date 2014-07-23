@mixin gradient(start, end)
    color #000
    background linear-gradient(12, 33, $start)
    background -ms-linear-gradient $start 0 $end $varname

body
    padding 0 10%
    font 10px "Arail black", Times
    color #111