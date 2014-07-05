<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Essentials;

/**
 * Description of SourceLine
 *
 * @author dobby007
 */
class SourceLine {
    private $line;
    private $level;
    private $curlyBracket;
    
    function __construct($line, $level, $curlyBracket = false) {
        $this->setLine($line);
        $this->setLevel($level);
        $this->setCurlyBracket($curlyBracket);
    }
    
    public function getLine() {
        return $this->line;
    }

    public function getLevel() {
        return $this->level;
    }

    public function hasOpenCurlyBracket() {
        return $this->curlyBracket === '}';
    }
    
    public function hasCloseCurlyBracket() {
        return $this->curlyBracket === '{';
    }
    
    public function hasCurlyBracket() {
        return $this->hasOpenCurlyBracket() || $this->hasCloseCurlyBracket();
    }

    public function setLine($line) {
        $this->line = (string) $line;
        return $this;
    }

    public function setLevel($level) {
        $this->level = (int) $level;
        return $this;
    }

    public function setCurlyBracket($curlyBracket) {
        if (in_array($curlyBracket, ['{', '}', false])) {
            $this->curlyBracket = $curlyBracket;
        }
        return $this;
    }
    
    public function length() {
        return strlen($this->line);
    }
    
    public function startsWith($text) {
        return strncmp($this->getLine(), $text, strlen($text)) === 0;
    }

    public function __toString() {
        return $this->getLevel() . ', ' . $this->getLine() . ' ' . $this->curlyBracket;
    }

}
