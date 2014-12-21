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

/**
 * Description of SourceLine
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class SourceLine {
    /** @var string */
    private $_line;
    /** @var SourceClosure */
    private $_closure;
    
    function __construct($line, SourceClosure $closure) {
        $this->setLine($line);
        $this->setClosure($closure);
    }
    
    public function getLine() {
        return $this->_line;
    }

    public function getLevel() {
        return $this->_closure->getLevel();
    }

    public function setLine($line) {
        $this->_line = (string) $line;
        return $this;
    }

    public function getClosure($level) {
        return $this->_closure;
    }
    
    public function setClosure($closure) {
        $this->_closure = $closure;
        return $this;
    }
    
    public function length() {
        return strlen($this->_line);
    }
    
    public function startsWith($text) {
        return strncmp($this->getLine(), $text, strlen($text)) === 0;
    }

    public function __toString() {
        return $this->getLevel() . ', ' . $this->getLine();
    }

}
