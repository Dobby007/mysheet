<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Internal\ParserExtensions;

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
        $ruleset->setRoot($this->getRoot());
        
        do {
            if ($curLine[0] == $firstLine[0]) {
                $selectors = explode(',', $curLine[1]);
                $ruleset->addSelectors($selectors);
            } else if ($curLine[0] == $firstLine[0] + 1) {
                $nextline = $context->getLine($context->getLineNumber() + 1);
                $declaration = $curLine[1];

                if ($nextline) {
                    if (
                        $firstLine[0] >= $nextline[0] ||
                        !Declaration::canBeDeclaration($nextline[1])
                    ) {
                        $ruleset->addDeclaration($declaration);
                        return $ruleset;
                    } else if ($nextline[0] > $curLine[0]) {
                        return $ruleset;
                    }
                }

                $ruleset->addDeclaration($declaration);
            } else {
                return false;
            }
        } while ($curLine = $context->nextline());

        if ($ruleset->countDeclarations() > 0) {
            return $ruleset;
        }


        return false;
    }
}
