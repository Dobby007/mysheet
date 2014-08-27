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

namespace MSSLib\ParserExtensions;

use MSSLib\Essentials\ParserExtension;
use MSSLib\Structure\VarDefinition;
use MSSLib\Error\ParseException;
/**
 * Description of RulesetParserExtension
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class VariableParserExtension extends ParserExtension
{
    public function parse() {
        $context = $this->getContext();
        $curLine = $context->curLine();
        if ($curLine->startsWith('$')) {
            var_dump($curLine);
            if (!preg_match('/([a-z_][a-z_0-9]*)\s*=\s*(.+)/i', substr($curLine->getLine(), 1), $matches)) {
                throw new ParseException(null, 'BAD_DEFINITION');
            }
            return new VarDefinition(null, $matches[1], $matches[2]);
        }


        return false;
    }
}
