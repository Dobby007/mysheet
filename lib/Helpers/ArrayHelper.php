<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
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

    public static function insert_pair(array &$input, $key, $val) {
        if (!is_int($key) && $key !== null) {
            $input[$key] = $val;
        } else {
            $input[] = $val;
        }
    }

    public static function concat(array &$input, $val1, $_val = null) {
        $values = array_slice(func_get_args(), 1);

        array_walk($values, function($value) use(&$input) {
            if (is_array($value)) {
                foreach ($value as $key => $value) {
                    self::insert_pair($input, $key, $value);
                }
            } else {
                $input[] = $value;
            }
        });
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
