<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Helpers;

/**
 * Class that helps to work with strings and provides basic operations to work with them in MySheet
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
abstract class StringHelper
{
    public static function parseStringUntil(&$input, $substr) {
        $text = $input;
        $textLen = strlen($text);
        $i = 0;
        
        while ($i < $textLen) {
            if ($text[$i] === $substr) {
                break;
            } else {
                $i++;
            }
        }
        
        if ($i < $textLen) {
            $input = substr($input, $i);
            return substr($text, 0, $i);
        } else {
            return false;
        }
    }
    
    public static function parseFunction(&$input, $spaceInArgs = false) {
        $text = ltrim($input);
        $arguments = false;
        $funcName = self::parseStringUntil($text, '(');
        if ($funcName === false) {
            return false;
        }
        
        $args_offset = strlen($funcName);
        $enclosedWithBrackets = StringHelper::parseEnclosedString($text);
        if ($enclosedWithBrackets) {
            $argString = ltrim(substr($enclosedWithBrackets, 1, -1));
            $arguments = StringHelper::parseSplittedString($argString, ',', !$spaceInArgs);
        }
        if (!empty($arguments) && !empty($funcName)) {
            $input = substr($input, $args_offset + strlen($enclosedWithBrackets));
            return [
                'name' => trim($funcName),
                'arguments' => $arguments
            ];
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
        
        if ($charQueue->isEmpty()) {
            return substr($string, 0, $i);
        }
        
        return false;
    }
    
    public static function parseSplittedString(&$string, $delimiter, $stopAtSpace = true) {
        $splittedList = [];
        $len = strlen($string);
        $offset = 0;
        $i = 0;
        
        while (true) {
            if ($i >= $len - 1) {
                $splittedList[] = substr($string, $offset, ++$i - $offset);
                break;
            }
            
            $char = $string[$i];
            if (self::getEnclosedChar($char)) {
                $enclosedPart = self::parseEnclosedString(substr($string, $i));

                if ($enclosedPart) {
                    $i += strlen($enclosedPart);
                    continue;
                }
            } else if ( $char === $delimiter || ($stopAtSpace === true && ctype_space($char)) ) {
                $splittedList[] = substr($string, $offset, $i - $offset);
                
                $i += self::countLeftSpaces(substr($string, $i));
                if ($i >= $len || $string[$i] !== $delimiter) {
                    break;
                } else {
                    $i++;
                }
                $i += self::countLeftSpaces(substr($string, $i));
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
    
    public static function countLeftSpaces($string) {
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
            if (!self::isSpaceSymbol($line[$i]) && $i > 0) {
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

}
