@mixin filter-grayscale(percent)
    -webkit-filter: grayscale($percent);
    -moz-filter: grayscale($percent);
    -ms-filter: grayscale($percent);
    -o-filter: grayscale($percent);
    filter: grayscale($percent);

@mixin transform()
    -o-transform $arguments
    -webkit-transform $arguments
    -moz-transform $arguments
    transform $arguments

.trynow-wdg
    overflow hidden
    .error
        display none
    .work-area
        overflow hidden
        
        .examples
            float left
            width 200pt
            border-radius 8px
            background-color #eee
            min-height 240px
            ul
                li
                    a
                        cursor pointer
                        color #333
                        display block
                        padding 8px 10px
                    a:hover
                        color orange
                    &.active a
                        padding-left 12px
                        background-color #ddd
        .source
            float: left;
            width: 400px;
            padding 8px 20px 0 10px;

        .result
            padding-top 8px
            overflow hidden
        
    textarea
        width 100%
        height 200px


.feature-gallery
    margin-top: 15px
    .features-wrapper
        overflow hidden
        .list
            overflow hidden
            text-align center
            li
                color black
                vertical-align top
                cursor pointer
                display inline-block
                width 72px
                height 72px
                margin 0 15px
                filter-grayscale 100%
                text-align center
            li:hover
            li.active
                border-color #aaa
                filter-grayscale 0%
    .slides
        .slide
            h2
                text-align center

section.style1
    margin 0
    overflow hidden
    border-radius 15px
    color white - 70lt
    background-color #222
    padding-bottom 10px
    h1
        margin 0 0 5px 0
        padding 4px 10px
        background-color #333
    p
        padding 0 10px

.important-block
    display block
    margin 0 auto
    width 700px
    .logo
        float left
        width 300px
        margin-right 20px
    .message
        overflow hidden