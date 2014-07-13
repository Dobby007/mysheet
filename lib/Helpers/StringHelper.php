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
    
    public static function getEnclosedCharMap() {
        static $charMap = [
            '"' => ['closeAt' => '"', 'wait' => true],
            '(' => ['closeAt' => ')', 'wait' => false],
            '[' => ['closeAt' => ']', 'wait' => false],
            '{' => ['closeAt' => '}', 'wait' => false],
            "'" => ['closeAt' => "'", 'wait' => true]
        ];
        return $charMap;
    }

    public static function getEnclosedChar($char) {
        $charMap = self::getEnclosedCharMap();
        
        if (isset($charMap[$char])) {
            return $charMap[$char];
        }
        
        return false;
    }
    
    public static function parseEnclosedString($string) {
        $charMap = self::getEnclosedCharMap();
        
        if (!isset($charMap[substr($string, 0, 1)])) {
            return false;
        }
        
//        var_dump('parse enclosed string: '. $string);
        
        $len = strlen($string);
        $charQueue = new \SplStack();
        
        for ($i = 0; $i < $len; $i ++) {
            $char = $string[$i];
            $enclosedChar = isset($charMap[$char]) ? $charMap[$char] : false;
            
            if (!$charQueue->isEmpty()) {
                $top = $charQueue->top();
                if ($top['closeAt'] === $char) {
                    $charQueue->pop();
                } else if ($top['wait'] === false && $enclosedChar) {
                    $charQueue->push($enclosedChar);
                }
            } else if ($enclosedChar) {
                $charQueue->push($enclosedChar);
            } else {
                break;
            }
        }
        
//        var_dump('parsing done: ' . substr($string, 0, $i) . '.');  
        if ($charQueue->isEmpty()) {
            return substr($string, 0, $i);
        }
        
        return false;
    }
    
    public static function parseSplittedString(&$string, $delimiter) {
        $len = strlen($string);
        $offset = 0;
        
        $splittedList = [];
        $delimiterMet = false;
        $i = 0;
        
//        function addFromOffset($charsCount) {
//            $splittedList[] = 
//        }
        
        while ($i < $len) {
            $char = $string[$i];
            if (self::getEnclosedChar($char)) {
                $enclosedPart = self::parseEnclosedString(substr($string, $i));
                    
//                var_dump('enclosed with ' . $char . ' at ' . $i . ', ' . $string, $enclosedPart, 'new start string = ' . substr($string, $i + strlen($enclosedPart)));
                if ($enclosedPart) {
                    $i += strlen($enclosedPart);
//                    var_dump('enclosed part = ' . $enclosedPart);
                    continue;
                }
                
            } else if ($char === $delimiter || ctype_space($char)) {
                $splittedList[] = substr($string, $offset, $i - $offset);
//                var_dump('old i: ' . $i);
                $i += self::countLeftSpaces(substr($string, $i));
                if ($string[$i] !== $delimiter) {
                    break;
                }
                $i++;
                $i += self::countLeftSpaces(substr($string, $i));
//                var_dump('new i: ' . $i . '-' . substr($string, $i));
                $offset = $i;
                continue;
            } else {
                $i++;
            }
        }
        
        if ($i >= $len - 1) {
            $splittedList[] = substr($string, $offset, $i - $offset);
        }
        
        $string = substr($string, $i);
        
        return $splittedList;
    }
    
    public static function countLeftSpaces($string) {
        $len = strlen($string);
        $i = 0;
        $count = 0;
        
        while ($i < $len && ctype_space($string[$i++])) {
            $count++;
        }
        
        return $count;
    }
//    public static function skipWhiteSpaceAnd($charsList, &$spacesSkipped, ) {
//        
//    }

}
