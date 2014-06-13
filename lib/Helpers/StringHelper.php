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
abstract class StringHelper {

    public static function parse_function($input) {
        $info = [];
        $input = trim($input);
        
        if (substr($input, -1) !== ')') {
            return false;
        }
        
        $tmp = strstr($input, '(');
        $arguments =  substr($tmp, 1, -1);
        $info['name'] = strstr($input, '(', true);
        
        if (preg_match_all('/\s*(\'.*\'|[^,\s]+)\s*(?:,\s*|$)/', $arguments, $matches, PREG_PATTERN_ORDER)) {
            $info['arguments'] = $matches[1];
            return $info;
        }
        
        
        return false;
    }
    
    public static function parseMetric($input) {
        $input = trim($input);
        $i = 0;
        $len = strlen($input);
        
        $unit_found = false;
        $metric = '';
        $unit = '';
        while ($i < $len) {
            if ($unit_found === false) {
                if (
                    ($input[$i] >= '0' && $input[$i] <= '9') ||
                    ($input[$i] == '.') ||
                    ($input[$i] == '-' && $i === 0)
                ) {
                    $metric .= $input[$i];
                    $i++;
                } else {
                    $unit_found = true;
                }
                
            } else if (strlen($metric) > 0) {
                if (ctype_space($input[$i])) {
                    return false;
                }
                
                $unit .= $input[$i];
                $i++;
            } else {
                return false;
            }
        }
        
        return ['metric' => floatval($metric), 'unit' => $unit];
    }

}
