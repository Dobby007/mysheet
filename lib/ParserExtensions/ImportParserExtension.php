<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\ParserExtensions;

use MySheet\Essentials\ParserExtension;
use MySheet\Structure\ImportDirective;

/**
 * Description of RulesetParserExtension
 *
 * @author dobby007
 */
class ImportParserExtension extends ParserExtension
{
    
    public function parse() {
        $context = $this->getContext();
        $firstLine = $curLine = $context->curline();
        
        if ($curLine->startsWith('@import ')) {
            $importDir = new ImportDirective(null, substr($curLine->getLine(), 7));
            return $importDir;
        }
        
        

        return false;
    }
}
