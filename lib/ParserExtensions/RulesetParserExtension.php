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
use MySheet\Helpers\StringHelper;
use MySheet\Helpers\ArrayHelper;
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
        do {
            $line = $curLine->getLine();
            echo $line;
            $declarations = StringHelper::parseSplittedString($line, ';', false);
            $allIsRight = ArrayHelper::jsAll($declarations, function($item) {
                return Declaration::canBeDeclaration($item);
            });
            
            if ($allIsRight) {
                $ruleset->addDeclarations($declarations);
            } else {
                break;
            }
        } while ($curLine = $context->nextLine());    
            
        if ($ruleset->countDeclarations() >= 0) {
            return $ruleset;
        }

        return false;
    }
}
