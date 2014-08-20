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
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
trait ParserCursorStateTrait {
    private $savedCursor = null;
    
    protected function saveCursorState() {
        $this->savedCursor = $this->getLineNumber();
    }

    protected function restoreCursorState() {
        if ($this->savedCursor !== null) {
            $this->setLineCursor($this->savedCursor);
            return true;
        }
        return false;
    }
}
