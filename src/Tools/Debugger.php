<?php

/*
 * Copyright 2014 dobby007 (Alexander Gilevich, alegil91@gmail.com).
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 */

namespace MSSLib\Tools;

/**
 * Description of Debugger
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class Debugger {

    private static $debugMode = false;

    public static function logString($str) {
        if (self::isDebugging()) {
            echo "\n" . $str;
        }
    }

    public static function logObjects($_objects) {
        if (self::isDebugging()) {
            foreach (func_get_args() as $arg) {
                if (is_object($arg) || is_array($arg)) {
                    var_dump($arg);
                } else {
                    echo "\n" . $arg . "\n";
                }
            }
        }
    }

    public static function setDebugMode($debugMode) {
        self::$debugMode = !!$debugMode;
    }

    public static function isDebugging() {
        return self::$debugMode;
    }

}
