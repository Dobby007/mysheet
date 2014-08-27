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
use MSSLib\Structure\Ruleset;
use MSSLib\Structure\Declaration;
use MSSLib\Helpers\StringHelper;
use MSSLib\Helpers\ArrayHelper;
/**
 * Description of RulesetParserExtension
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class RulesetParserExtension extends ParserExtension
{
    
    public function parse() {
        $context = $this->getContext();
        //Check if children exist
        if ($context->curClosure()->countChildren() < 1) {
            return false;
        }
        
        $firstLine = $curLine = $context->curLine();
        $ruleset = new Ruleset(null);
        
        do {
            $line = $curLine->getLine();
            $selectors = StringHelper::parseSplittedString($line, ',', false);
            try {
                $ruleset->addSelectors($selectors);
            } catch (\MSSLib\Error\ParseException $exc) {
                return false;
            }
        } while ($curLine = $context->nextLine());
        
        $context->goToClosure(1);
        $curLine = $context->curLine();
        $lastLine = $context->curClosure()->countLines() - 1;
        if ($context->curClosure()->hasChildren()) {
            $lastLine--;
        }
        do {
            try {
                $ruleset->addDeclarations($curLine->getLine());
            } catch (\MSSLib\Error\ParseException $exc) {
                $context->prevLine();
                break;
            }
        } while ($context->getCurrentLineIndex() < $lastLine && $curLine = $context->nextLine());    
            
        return $ruleset;
    }
}
