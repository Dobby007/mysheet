@import url(http://fonts.googleapis.com/css?family=Open+Sans&subset=latin,cyrillic);
@import "site-common.mss"
@import "basic.mss"
@import "pages.mss"

@mixin filter-grayscale(percent)
    -webkit-filter: grayscale($percent);
    -moz-filter: grayscale($percent);
    -ms-filter: grayscale($percent);
    -o-filter: grayscale($percent);
    filter: grayscale($percent);

html
    height 100%
    body
        text-align left
        line-height 105%
        color #777
        height 100%
        font-family: 'Open Sans', sans-serif;
        .wrapper
            position relative
            min-height 100%
            #header
                position fixed
                top 0
                left 0
                width 100%
                color #fff
                background-color rgb(0, 0, 0)
                background-color rgba(0, 0, 0, 60%)
                z-index 999
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
                position relative
                margin-bottom 40px
                padding 10px
                background-color mainColor
                color white
                h1,h2,h3
                    color #fff !important
                .version-note
                    letter-spacing 2.1px
                    text-transform uppercase
                    font-family avenir,helvetica,arial,sans-serif
                    margin 15px 0
                .shaddowed
                    text-shadow 3px 3px 10px
                .bg-trans
                    background url(/css/images/circles_top.png) 0 100%
                    height: 20px
                    margin-bottom -18px
                    width 100%
                    position absolute
                    bottom 0
                    left 0
            #pcontent.without-header
                margin-top 0
            #pcontent
                margin-top 30px
                position relative
                .content-sidebar
                    position absolute
                    top 0
                    height 100%
                    width 20%
                    background-color mainColor/// rgb(62, 161, 218)
                    border-right 10px solid 18b /// rgb(62, 161, 200)
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
                            &.active > a
                                border 0
                                background-color aa
                .text-content
                    padding: 10px 100px
                    a
                        color blue
                h1,h2,h3,h4,h5,h6
                    color white - 70lt
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
                background-color aaa
                color white
                padding 12px 0 5px
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
                .bg-trans
                    background url(/css/images/circles_bottom.png)
                    height 40px
                    margin-top -32px
                    width 100%
                    position absolute
                    top 0
                    left 0

pre.with-trynow
    padding-bottom 20px
    position relative
    .trynow-block
        display none
        position absolute
        bottom 0
        right 0
        margin 0 5px 5px 0
        a
            letter-spacing 1px
    &:hover .trynow-block
        display block

.main-presentation
    .hint
        border 4px dashed #ffc
        color #ffc
        font-size 1.2em
        font-style italic
        border-radius 50%
        position absolute
        top 0
        right 0
        padding 10px 35px
        max-width 20%
        text-align center
        line-height 1em
        a
            border-bottom-width 1px
            border-bottom-style dashed
        a:hover
            color #FFB900
        &.hint1
            top 10px
            right 20px
            transform rotate(-5deg)
            div.freepath
                background url(/css/images/freepath.png) no-repeat
                position: absolute
                top 100%
                left 70px
                height 200px
                width 200px
        &.hint2
            top auto
            bottom 14px
            right 200px
            padding-bottom 14px