<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\ParserExtensions;

use MySheet\Essentials\ParserExtension;
use MySheet\Structure\Ruleset;
use MySheet\Structure\Declaration;
/**
 * Description of RulesetParserExtension
 *
 * @author dobby007
 */
class VariableParserExtension extends ParserExtension
{
    
    public function parse() {
        $context = $this->getContext();
        $curLine = $context->curline();
        if ($curLine->startsWith('$')) {
            $varname = substr($curLine->getLine(), 1);
            $this->getRoot()->getVars()->set($varname, $value);
        }


        return false;
    }
}
