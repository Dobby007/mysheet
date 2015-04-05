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
use MSSLib\Structure\AtRule;

/**
 * Description of RulesetParserExtension
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class AtRuleParserExtension extends ParserExtension
{
    public function parse() {
        $context = $this->getContext();
        $firstLine = $curLine = $context->curLine();
        
        if ($curLine->startsWith('@')) {
            if (($declaration = $this->parseAtRuleDeclaration($curLine))) {
                $atRule = new AtRule(null);
                $atRule->setName($declaration['name'])->setParameters($declaration['parameters']);
                return $atRule;
            }
        }
        
        return false;
    }
    
    protected function parseAtRuleDeclaration($sourceLine) {
        if (preg_match('/^\@([a-z\-]+)(?:\s+|$)(.*)$/i', $sourceLine->getLine(), $matches)) {
            return [
                'name' => $matches[1],
                'parameters' => rtrim($matches[2], ';')
            ];
        }
        return false;
    }
}
