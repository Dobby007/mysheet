<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MSSLib\ParserExtensions;

use MSSLib\Essentials\ParserExtension;
use MSSLib\Structure\ImportDirective;

/**
 * Description of RulesetParserExtension
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class ImportParserExtension extends ParserExtension
{
    
    public function parse() {
        $context = $this->getContext();
        $firstLine = $curLine = $context->curLine();
        
        if ($curLine->startsWith('@import ')) {
            $importDir = new ImportDirective(null, substr($curLine->getLine(), 7));
            return $importDir;
        }
        

        return false;
    }
}
