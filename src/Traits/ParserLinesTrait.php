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

namespace MSSLib\Traits;

use MSSLib\Essentials\SourceLine;

/**
 * Description of HandlerClassTrait
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
trait ParserLinesTrait {
    /**
     * The array of lines
     * @var SourceLine[]
     */
    private $lines;
    private $curLine = false;
    
    /**
     * @return SourceLine Move cursor to the previous line
     */
    public function prevline($stop_at_curly_lines = false) {
        if ($this->moveLineCursor(-1)) {
            return $this->curline();
        }
        return false;
    }
    
    /**
     * @return SourceLine Move cursor to the next line
     */
    public function nextline($stop_at_curly_lines = false) {
        if ($this->moveLineCursor(+1)) {
            return $this->curline();
        }
        return false;
    }

    /**
     * @return SourceLine Get current line over cursor
     */
    public function curline() {
        if ($this->curLine === false || count($this->lines) === 0) {
            return false;
        }

        return $this->lines[$this->curLine];
    }

    /**
     * @return int Get current line number
     */
    public function getLineNumber() {
        return $this->curLine;
    }
    
    /**
     * @return SourceLine Get special line without moving cursor to it
     */
    public function getLine($line_number) {
        if ($line_number >= count($this->lines)) {
            return false;
        }

        return $this->lines[$line_number];
    }

    /**
     * @return null Set cursor to the special line number
     */
    public function setLineCursor($nn) {
        if ($this->canBeSetTo($nn)) {
            $this->curLine = intval($nn);
        }
    }
    
    public function canBeSetTo($nn) {
        if ($nn >= 0 && $nn < count($this->lines)) {
            return true;
        }
        return false;
    }
    
    /**
     * @return boolean Can cursor move through $lines_count or not
     */
    public function canMoveOver($lines_count) {
        $future_ln = $this->getLineNumber() + $lines_count;
        
        if ($this->canBeSetTo($future_ln)) {
            return true;
        }
        return false;
    }
    
    /**
     * @return int Move cursor through $lines_count lines
     */
    public function moveLineCursor($lines_count) {
        $lines_count = intval($lines_count);
        
        if ($this->canMoveOver($lines_count)) {
            $this->curLine += $lines_count;
            return $this->curLine;
        }
        return false;
    }
    
    /**
     * Reset cursor and set its' position to the line with zero index
     * @return null
     */
    public function resetCursor() {
        $this->setLineCursor(0);
    }
    
}
