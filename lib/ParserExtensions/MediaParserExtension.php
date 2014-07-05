<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\ParserExtensions;

use MySheet\Essentials\ParserExtension;
use MySheet\Structure\MediaRequest;
/**
 * Description of RulesetParserExtension
 *
 * @author dobby007
 */
class MediaParserExtension extends ParserExtension
{
    
    public function parse() {
        $context = $this->getContext();
        $firstLine = $curLine = $context->curline();
        
        if ($curLine->startsWith('@media ')) {
            $mediaReq = new MediaRequest(null);
            $mediaReq->setRequest(substr($curLine->getLine(), 7));
            return $mediaReq;
        }
        
        

        return false;
    }
}
