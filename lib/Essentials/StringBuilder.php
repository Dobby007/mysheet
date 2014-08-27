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

namespace MSSLib\Essentials;

use MSSLib\Helpers\ArrayHelper;

/**
 * Description of StringBuilder
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class StringBuilder {
    private $lines = array();
    
    public function __construct(array $lines = []) {
        $this->addLines($lines);
    }

    public function getLines() {
        return $this->lines;
    }
    
    public function count() {
        return count($this->lines);
    }
        
    public function addLine($line) {
        if (is_string($line)) {
            $this->lines[] = $line;
        }
        return $this;
    }
    
    public function addLines($lines) {
        if (is_string($lines)) {
            $this->addLine($lines);
        } else if (is_array($lines) && !empty($lines)) {
            ArrayHelper::concat($this->lines, $lines);
        } else if ($lines instanceof StringBuilder) {
            ArrayHelper::concat($this->lines, $lines->getLines());
        }
        return $this;
    }
    
    public function appendText($text, $lfCheck = true) {
        end($this->lines);
        $line = null;
        if (!empty($this->lines)) {
            $line = &$this->lines[key($this->lines)];
        }
        if (is_string($text)) {
            if ($lfCheck === true) {
                $text = preg_split('/\r?\n/', $text);
            } else {
                $text = [$text];
            }
        } else if ($text instanceof StringBuilder) {
            $text = $text->getLines();
        }
        
        if (is_array($text) && !empty($text)) {
            if ($line !== null) {
                $line .= $text[0];
            } else {
                $this->addLine($text[0]);
            }
            
            for ($i = 1; $i < count($text); $i++) {
                $this->addLine($text[$i]);
            }
        }
        
        return $this;
    }
    
    public function processLines($prefix, $suffix) {
        $this->lines = ArrayHelper::processLines($this->lines, $prefix, $suffix, '');
        return $this;
    }
    
    public function join($separator = "\n") {
        return implode($separator, $this->lines);
    }
    
    public function __toString() {
        return $this->join();
    }

    
}
