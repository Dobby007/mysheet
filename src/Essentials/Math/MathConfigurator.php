<?php
/**
 * Created by PhpStorm.
 * User: dobby007
 * Date: 26.05.2015
 * Time: 21:14
 */

namespace MSSLib\Essentials\Math;
use MSSLib\EmbeddedClasses\MetricClass;
use MSSLib\EmbeddedClasses\ColorClass;


class MathConfigurator
{
    public static function registerOperations()
    {
        MetricClass::registerOperations();
        ColorClass::registerOperations();
    }

}