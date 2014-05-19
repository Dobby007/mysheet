<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Traits;


/**
 * Description of HandlerClassTrait
 *
 * @author dobby007
 */
trait ParserLinesTrait {
    private $lines;
    private $curLine = false;
    
    
    public function prevline() {
        if ($this->moveLineCursor(-1)) {
            return $this->curline();
        }
        return false;
    }

    public function nextline() {
        if ($this->moveLineCursor(+1)) {
            return $this->curline();
        }
        return false;
    }

    public function curline() {
        if ($this->curLine === false || count($this->lines) === 0)
            return false;

        return $this->lines[$this->curLine];
    }

    public function getLineNumber() {
        return $this->curLine;
    }
    
    public function getLine($line_number) {
        if ($line_number >= count($this->lines))
            return false;

        return $this->lines[$line_number];
    }

    public function setLineCursor($nn) {
        $this->curLine = intval($nn);
    }
    
    public function canMoveOver($lines_count) {
        $future_ln = $this->getLineNumber() + $lines_count;
        
        if ($future_ln > 0 && $future_ln < count($this->lines)) {
            return true;
        }
        return false;
    }
    
    public function moveLineCursor($lines_count) {
        $lines_count = intval($lines_count);
        
        if ($this->canMoveOver($lines_count)) {
            $this->curLine += $lines_count;
            return $this->curLine;
        }
        return false;
    }
    
    public function resetCursor() {
        $this->setLineCursor(0);
    }
}
