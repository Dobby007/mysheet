<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\ParserExtensions;

use MySheet\Essentials\ParserExtension;
use MySheet\Structure\VarDefinition;
/**
 * Description of RulesetParserExtension
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class VariableParserExtension extends ParserExtension
{
    
    public function parse() {
        $context = $this->getContext();
        $curLine = $context->curline();
        if ($curLine->startsWith('$')) {
            if (!preg_match('/([a-z_][a-z_0-9]*)\s*=\s*(.+)/i', substr($curLine->getLine(), 1), $matches)) {
                //throw
                return false;
            }
//            var_dump('parse var', $matches);
            return new VarDefinition(null, $matches[1], $matches[2]);
        }


        return false;
    }
}
