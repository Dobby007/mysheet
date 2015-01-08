<?php

/*
 *  Copyright 2014 Alexander Gilevich (alegil91@gmail.com)
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at 
 * 
 *      http://www.apache.org/licenses/LICENSE-2.0
 */

namespace MSSLib\Helpers;

/**
 * Description of ArrayHelper
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
abstract class ArrayHelper {

    public static function implode_objects($glue, array $source, $method_name = null, $_args = null) {
        $proccessed = [];

        if (!empty($method_name) && is_string($method_name)) {
            $right_args = array_slice(func_get_args(), 3);
            array_walk($source, function($item) use($method_name, $right_args, &$proccessed) {
                $proccessed[] = call_user_func_array(array($item, $method_name), $right_args);
            });
        } else {
            $proccessed = $source;
        }

        return implode($glue, $proccessed);
    }

    public static function insert_value(array &$input, $key, $val) {
        if (!is_int($key) && $key !== null) {
            $input[$key] = $val;
        } else {
            $input[] = $val;
        }
    }
    
    public static function append(array &$input, $value) {
        if (is_array($value)) {
            foreach ($value as $key => $value) {
                if (!is_int($key)) {
                    $input[$key] = $value;
                } else {
                    $input[] = $value;
                }
            }
        } else {
            $input[] = $value;
        }
    }
    
    public static function concat(array &$input, $val1, $_val = null) {
        $values = array_slice(func_get_args(), 1);

        foreach($values as $value) {
            self::append($input, $value);
        }
    }

    public static function filter(array $array, callable $callback = null) {
        if ($callback == null) {
            $callback = function($key, $val) {
                return (bool) $val;
            };
        }
        $return = array();
        foreach ($array as $key => $val) {
            if ($callback($key, $val)) {
                $return[$key] = $val;
            }
        }
        return $return;
    }
    
    public static function map(callable $callback, array $array) {
        $return = array();
        foreach ($array as $key => $val) {
            $return[$key] = $callback($val);
        }
        return $return;
    }
    
    public static function processLines(array $lines, $prefix, $suffix, $separator, $ignoreLastLine = true) {
        $count = count($lines);
        for ($i = 0; $i < $count; $i++) {
            $lines[$i] = $prefix . $lines[$i] . $suffix . 
                ($i === $count - 1 && $ignoreLastLine ? '' : $separator);
            
        }
        return $lines;
    }
    
    public static function implodeLines(array $lines, $prefix, $suffix, $separator) {
        $count = count($lines);
        for ($i = 0; $i < $count; $i++) {
            $lines[$i] = $prefix . $lines[$i] . $suffix;
        }
        return implode($separator, $lines);
    }

    
    public static function jsAll($array, callable $callback) {
        foreach ($array as $item) {
            if (!$callback($item)) {
                return false;
            }
        }
        return true;
    }
}
