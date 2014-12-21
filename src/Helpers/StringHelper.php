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
 * Class that helps to work with strings and provides basic operations to work with them in MySheet
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
abstract class StringHelper
{
    protected static $enclosedCharMap = [
        '"' => ['closeAt' => '"', 'wait' => true],
        '(' => ['closeAt' => ')', 'wait' => false],
        '[' => ['closeAt' => ']', 'wait' => false],
        '{' => ['closeAt' => '}', 'wait' => false],
        "'" => ['closeAt' => "'", 'wait' => true]
    ];
    
    public static function parseStringUntil(&$input, $substr) {
        $text = $input;
        $textLen = strlen($text);
        $i = strpos($input, $substr);
        
        if ($i !== false) {
            $input = substr($input, $i);
            return substr($text, 0, $i);
        } else {
            return false;
        }
    }
    
    public static function parseFunction(&$input, $spaceInArgs = false, $nameChecking = true) {
        $text = ltrim($input);
        $arguments = false;
        $funcName = self::parseStringUntil($text, '(');
        if ($funcName === false) {
            return false;
        }
        
        $args_offset = strlen($funcName);
        $funcName = trim($funcName);
        if ($nameChecking && !preg_match('/^[a-z\-_][[a-z0-9\-_]*$/i', $funcName)) {
            return false;
        }
        
        $enclosedWithBrackets = StringHelper::parseEnclosedString($text);
        if ($enclosedWithBrackets) {
            $argString = ltrim(substr($enclosedWithBrackets, 1, -1));
            $arguments = StringHelper::parseSplittedString($argString, ',', !$spaceInArgs);
        }
        if (!empty($arguments) && !empty($funcName)) {
            $input = substr($input, $args_offset + strlen($enclosedWithBrackets));
            return [
                'name' => $funcName,
                'arguments' => $arguments
            ];
        }
        return false;
    }
    
    public static function parseMetric(&$input) {
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
    
    public static function parseEnclosedString(&$string) {
        $charMap = self::$enclosedCharMap;
        $stringCopy = $string;
        
        if (!isset($charMap[substr($string, 0, 1)])) {
            return false;
        }
        
        $len = strlen($string);
        $charQueue = new \SplStack();
        $topCharInQueue = null;
        
        for ($i = 0; $i < $len; $i ++) {
            $char = $string[$i];
            if (!$charQueue->isEmpty()) {
                if ($topCharInQueue['closeAt'] === $char) {
                    $charQueue->pop();
                } else if ($topCharInQueue['wait'] === false && isset($charMap[$char])) {
                    $charQueue->push($charMap[$char]);
                }
            } else if (isset($charMap[$char])) {
                $topCharInQueue = $charMap[$char];
                $charQueue->push($charMap[$char]);
            } else {
                break;
            }
        }
        
        if ($charQueue->isEmpty()) {
            $string = substr($string, $i);
            return substr($stringCopy, 0, $i);
        }
        
        return false;
    }
    
    public static function parseSplittedString(&$string, $delimiters, $stopAtSpace = true, &$metDelim = null) {
        $splittedList = [];
        $len = strlen($string);
        $offset = 0;
        $i = 0;
        
        if (is_string($delimiters)) {
            $delimiters = [$delimiters];
        }
        
        while (true) {
            if ($i >= $len) {
                $splittedList[] = substr($string, $offset, ++$i - $offset);
                break;
            }
            
            $char = $string[$i];
            if (isset(self::$enclosedCharMap[$char])) {
                $possibleEnclosed = substr($string, $i);
                $enclosedPart = self::parseEnclosedString($possibleEnclosed);

                if ($enclosedPart) {
                    $i += strlen($enclosedPart);
                    continue;
                } else {
                    $i++;
                }
            } else if (
                    ($metDelim && $metDelim === $char) || 
                    (!$metDelim && in_array($char, $delimiters)) || 
                    ($stopAtSpace === true && ctype_space($char)) 
            ) {
                $splittedList[] = substr($string, $offset, $i - $offset);
                if (!$metDelim) {
                    $metDelim = $char;
                }
                $i += self::countPrecSpaces(substr($string, $i));
                if ($i >= $len || !in_array($char, $delimiters)) {
                    break;
                } else {
                    $i++;
                }
                $i += self::countPrecSpaces(substr($string, $i));
                $offset = $i;
                continue;
            } else {
                $i++;
            }
        }
//        var_dump($splittedList);
        $string = substr($string, $i);
        
        return array_map('trim', $splittedList);
    }
    
    public static function getEnclosedCharMap() {
        return self::$enclosedCharMap;
    }

    public static function getEnclosedChar($char) {
        if (isset(self::$enclosedCharMap[$char])) {
            return self::$enclosedCharMap[$char];
        }
        
        return false;
    }
    
    public static function countPrecSpaces($string) {
        $len = strlen($string);
        $i = 0;
        $count = 0;
        
        while ($i < $len && ctype_space($string[$i++])) {
            $count++;
        }
        
        return $count;
    }

    public static function replaceSubstring($substring, array $replacements, $string) {
        $result = [];
        foreach ($replacements as $replacement) {
            $result[] = str_replace($substring, $replacement, $string);
        }
        return $result;
    }
    
    public static function getClassName($classname) {
        return substr($classname, strrpos($classname, '\\') + 1);
    }
    
    public static function rtrimBySubstring($string, $substr) {
        $substrLen = strlen($substr);
        while (substr($string, -$substrLen) === $substr) {
            $string = substr($string, 0, -$substrLen);
        }
        return $string;
    }
    
    /**
     * 
     * @param string $line Text line
     * @param boolean $count_tabs True if you want to count \t characters instead of spaces
     * @return int|boolean Returns the number of spaces till the first meaning character
     */
    public static function countLineSpaces($line, $count_tabs = false) {
        $linelen = strlen($line);
        $i = 0;

        while ($i < $linelen) {
            if ($line[$i] !== '\t' && $line[$i] !== ' ' && $i > 0) {
                return $i;
            } else if ($line[$i] === "\r" || $line[$i] === "\n") {
                return false;
            } else if ($i === 0 && !self::isSpaceSymbol($line[$i])) {
                return 0;
            }

            $i++;
        }

        return false;
    }

    public static function isSpaceSymbol($char) {
        if ($char === ' ' || $char === "\t")
            return true;

        return false;
    }
    
    /**
     * 
     * @param string $string
     * @param mixed $prefixes
     */
    public static function stringStartsWith($string, $prefixes) {
        if (is_string($prefixes)) {
            return substr($string, 0, strlen($prefixes)) === $prefixes;
        } else if (is_array($prefixes)) {
            foreach ($prefixes as $prefix) {
                if (substr($string, 0, strlen($prefix)) === $prefix) {
                    return true;
                }
            }
        }
        return false;
    }

}
