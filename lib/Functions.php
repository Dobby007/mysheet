<?php

namespace MySheet;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function array_insert_pair(array &$input, $key, $val) {
    if (!is_int($key) && $key !== null) {
        $input[$key] = $val;
    } else {
        $input[] = $val;
    }
}

function array_concat(array &$input, $val1, $_val = null) {
    $values = array_slice(func_get_args(), 1);
    
    array_walk($values, function($value) use(&$input) {
        if (is_array($value)) {
            foreach ($value as $key => $value) {
                array_insert_pair($input, $key, $value);
            }
        } else {
            $input[] = $value;
        }
        
    });
}