<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
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
            $ruleset->addSelectors($selectors);
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
