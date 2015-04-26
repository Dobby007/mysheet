MySheet - CSS Preprocessor and Parser written in PHP
=======
[![Latest Stable Version](https://poser.pugx.org/mysheet/mysheet/v/stable.svg)](https://packagist.org/packages/mysheet/mysheet) [![Total Downloads](https://poser.pugx.org/mysheet/mysheet/downloads.svg)](https://packagist.org/packages/mysheet/mysheet) [![Monthly Downloads](https://poser.pugx.org/mysheet/mysheet/d/monthly.png)](https://packagist.org/packages/mysheet/mysheet) [![Latest Unstable Version](https://poser.pugx.org/mysheet/mysheet/v/unstable.svg)](https://packagist.org/packages/mysheet/mysheet) [![License](https://poser.pugx.org/mysheet/mysheet/license.svg)](https://packagist.org/packages/mysheet/mysheet)

**Official website**: http://mss.flydigo.com

**Get started**: http://mss.flydigo.com/getStarted

**Documentation**: http://mss.flydigo.com/docs

**Author**: Alexander Gilevich ([about me](http://mss.flydigo.com/about))


What is it?
------
MySheet Library is the open-source CSS preprocessor and parser written in PHP. It has full backward compatibility with pure CSS. MySheet was highly inspired by the simplicity of Stylus for Node.js. It also has some solutions from other CSS preprocessors. 


Advantages
---

- Simplicity and flexibility of writing web-site styles for designer
- Styles can be adjusted from backend making it easy to control styles of your site even without proffessional knowledge of CSS
- Extensibility and support of plugins
- Compatibility with pure CSS


Examples
---

**Example #1 - hierarchical structure**:

    body
        padding 0
        margin 0
        .wrapper
            margin 0 auto
            width 50%

results in the following CSS:

    body {
        padding: 0;
        margin: 0
    }

    body .wrapper {
        margin: 0 auto;
        width: 50%
    }

**Example #2 - mixins**:

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

results in the following CSS:

    img {
        -webkit-filter: grayscale(100%);
        -moz-filter: grayscale(100%);
        -ms-filter: grayscale(100%);
        -o-filter: grayscale(100%);
        filter: grayscale(100%)
    }

    img:hover {
        -webkit-filter: grayscale(0%);
        -moz-filter: grayscale(0%);
        -ms-filter: grayscale(0%);
        -o-filter: grayscale(0%);
        filter: grayscale(0%)
    }

**Example #3 - arithmetic expressions and comments**:

    .object
        color #a50c5b - 50sat /* decrease saturation by 50% */
        background-color #a50c5b + 50lt /* make color lighter by 50 percent */

results in the following CSS:

    .object {
        color: #79385a;
        background-color: #fab6d9
    }

**Example #4 - arithmetic expressions in combine with variables**:

    $wrapper_height = 50%
    .wrapper
        height $wrapper_height
        top $wrapper_height / 2

results in the following CSS:

    .wrapper {
        height: 50%;
        top: 25%
    }

and much much more...

License
---

MySheet Library is open-source project. It is licensed under Apache License, Version 2.0. 
