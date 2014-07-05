@mixin gradient(start, end)
    color #000
    background linear-gradient 12 33 $start
    background -ms-linear-gradient $start 0 $end

body
    padding 0 10%
    font 10px Arial, "Times New Roman"
    color #111