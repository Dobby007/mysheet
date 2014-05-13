<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Plugins\Mixin;

use MySheet\Essentials\ParserExtension;

/**
 * Description of MixinParserExtension
 *
 * @author dobby007
 */
class MixinParserExtension extends ParserExtension {
    
    protected function parse() {
        $firstLine = $curLine = $this->curline();
        
        if (substr($firstLine[1], 0, 6) !== '@mixin')
            return false;
        
        if ($firstLine[0] !== 0)
            throw new ParseException(ErrorTable::E_BAD_INDENTATION);
        
        
        do {
            
        } while ($curLine = $this->nextline());
    }
}
