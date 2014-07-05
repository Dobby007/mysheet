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
class RulesetParserExtension extends ParserExtension
{
    
    public function parse() {
        $context = $this->getContext();
        $firstLine = $curLine = $context->curline();
        $ruleset = new Ruleset(null);
        
        do {
            if ($curLine->getLevel() == $firstLine->getLevel()) {
                $selectors = explode(',', $curLine->getLine());
                $ruleset->addSelectors($selectors);
            } else if ($curLine->getLevel() == $firstLine->getLevel() + 1) {
                $nextline = $context->getLine($context->getLineNumber() + 1);
                $declaration = $curLine->getLine();
//                var_dump($curLine);
                //if ruleset contains no declarations and just has children
                
                
                if ($nextline) {
                    if (
                        $nextline->getLevel() > $curLine->getLevel() ||
                        !Declaration::canBeDeclaration($declaration)
                    ) {
                        $context->prevline();
                        break;
                    } else if (
                        $firstLine->getLevel() >= $nextline->getLevel() ||
                        !Declaration::canBeDeclaration($nextline->getLine())
                    ) {
                        $ruleset->addDeclarations($declaration);
                        break;
                    }
                }

                $ruleset->addDeclarations($declaration);
            } else {
                return false;
            }
            
        } while ($curLine = $context->nextline());
                
        
        if ($ruleset->countDeclarations() >= 0) {
            return $ruleset;
        }


        return false;
    }
}
