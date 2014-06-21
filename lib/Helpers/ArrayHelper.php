<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Helpers;

/**
 * Description of ArrayHelper
 *
 * @author dobby007
 */
abstract class ArrayHelper {

    public static function implode_objects($glue, array $source, $method_name = null, $_args = null) {
        $result = [];

        if (!empty($method_name) && is_string($method_name)) {
            $right_args = array_slice(func_get_args(), 3);
            array_walk($source, function($item) use($method_name, $right_args, &$result) {
                $result[] = call_user_method_array($method_name, $item, $right_args);
            });
        }

        return implode($glue, $result);
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
    
    

}
