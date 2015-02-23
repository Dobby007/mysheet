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
/**
 * Description of RulesetParserExtension
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class RulesetParserExtension extends ParserExtension
{
    
    public function parse() {
        $context = $this->getContext();
        // Check if children exist
        if ($context->curClosure()->countChildren() < 1) {
            return false;
        }
        
        
        $topClosure = $context->curClosure();
        $firstLine = $curLine = $context->curLine();
        $ruleset = new Ruleset(null);
        
        $getNextLineIndexFunc = function ($delimiter) use ($context, $topClosure) {
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
        };
        
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
        
        // move to a next closure
        $context->goToClosure(1);
        
        $lastLineIndex = $getNextLineIndexFunc(';');
        
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
        
        
        $lastClosure = $context->curClosure();
        $curLine = $context->curLine();
        do {
            try {
                $ruleset->addDeclarations($curLine->getLine());
            } catch (\MSSLib\Error\ParseException $exc) {
                $context->prevLine();
                if ($exc->getErrorCode() !== 'BAD_DECLARATION') {
                    throw $exc;
                }
                break;
            }
            
            if ($context->curClosure() !== $lastClosure) {
                $lastLineIndex = $getNextLineIndexFunc(';');
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
        
        return $ruleset;
    }
}
