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
use MSSLib\Helpers\StringHelper;
use MSSLib\Structure\Declaration;
/**
 * Description of RulesetParserExtension
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class RulesetParserExtension extends ParserExtension
{
    public function parse() {
        $context = $this->getContext();
        $topClosure = $context->curClosure();
        
        // Check if children exist
        if ($context->curClosure()->countChildren() < 1) {
            $rules = $this->parseDeclarations($topClosure);
            return $rules;
        }
        
        $firstLine = $curLine = $context->curLine();
        $ruleset = new Ruleset(null);
        
        // read all selectors
        $this->parseSelectorsTo($ruleset);
        
        // move to a next closure
        $context->goToClosure(1);
        $lastLineIndex = $this->getLastLineIndex(';', $topClosure);
        
        // we're already at a next closure and if this closure has only 1 line, then we have got to rewind a bit
        // and return created ruleset
        // e.g.:
        // body
        //     .wrapper
        //         color red
        if ($lastLineIndex < 0) {
            $context->prevLine(true);
            return $ruleset;
        }
        
        $declarations = $this->parseDeclarations($topClosure);
        $ruleset->addDeclarations($declarations);
        
        return $ruleset;
    }
    
    private function parseSelectorsTo($ruleset) {
        $context = $this->getContext();
        $curLine = $context->curLine();
        // read all selectors
        do {
            $line = $curLine->getLine();
            $selectors = StringHelper::parseSplittedString($line, ',', false);
            try {
                $ruleset->addSelectors($selectors);
            } catch (\MSSLib\Error\ParseException $exc) {
                return false;
            }
        } while ($curLine = $context->nextLine());
    }
    
    private function parseDeclarations($topClosure) {
        $context = $this->getContext();
        $lastClosure = $context->curClosure();
        $curLine = $context->curLine();
        $lastLineIndex = $this->getLastLineIndex(';', $topClosure);
        $parsedDeclarations = [];
        do {
            try {
                $this->parseDeclarationString($curLine->getLine(), $parsedDeclarations);
            } catch (\MSSLib\Error\ParseException $exc) {
                $context->prevLine();
                if ($exc->getErrorCode() !== 'BAD_DECLARATION') {
                    throw $exc;
                }
                break;
            }
            
            if ($context->curClosure() !== $lastClosure) {
                $lastLineIndex = $this->getLastLineIndex(';', $topClosure);
                $lastClosure = $context->curClosure();
            }
        } while (
            (
                ($lastLineIndex !== false && $context->getCurrentLineIndex() < $lastLineIndex) || ($lastLineIndex === false)
            ) && 
            (
                $curLine = $context->nextLine(true)
            )
        );
        return $parsedDeclarations;
    }
    
    private function parseDeclarationString($sourceRules, array &$declarations) {
        $splittedRules = StringHelper::parseSplittedString($sourceRules, ';', false);   
        if (is_array($splittedRules)) {
            foreach ($splittedRules as $rule) {
                // ignore empty strings: they can appear if user typed semicolon several times
                if (!empty($rule)) {
                    $declarations[] = new Declaration($rule);
                }
            }
        }
    }
    
    private function getLastLineIndex($delimiter, $topClosure) {
        $context = $this->getContext();
        $curLine = $context->curLine();
        $countLines = $context->curClosure()->countLines();
        $lastLineIndex = $countLines - 1;
        $lastLine = $context->getLine($lastLineIndex);
        $lastLineEndsWithDelimiter = $lastLine && $lastLine->endsWith($delimiter);
        $canJumpOutOfClosure = $context->curClosure()->getParent() !== $topClosure;

        if (!$lastLineEndsWithDelimiter && $context->curClosure()->hasChildren()) {
            // if there are any child closures
            $lastLineIndex--;
        } else if (
                ($context->curClosure()->hasChildren() || $canJumpOutOfClosure) && 
                $lastLineEndsWithDelimiter === true
        ) {
            // otherwise if last line ends with specified delimiter and it is not a first level child then there may exist other declarations below upper limit
            $lastLineIndex = false;
        }
        return $lastLineIndex;
    }
}
