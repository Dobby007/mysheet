@mixin filter-grayscale(percent)
    -webkit-filter: grayscale($percent);
    -moz-filter: grayscale($percent);
    -ms-filter: grayscale($percent);
    -o-filter: grayscale($percent);
    filter: grayscale($percent);

img
    filter-grayscale 100%
img:hover
    filter-grayscale 0%