<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MSSLib\ParserExtensions;

use MSSLib\Essentials\ParserExtension;
use MSSLib\Structure\MediaRequest;
/**
 * Description of RulesetParserExtension
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class MediaParserExtension extends ParserExtension
{
    
    public function parse() {
        $context = $this->getContext();
        $firstLine = $curLine = $context->curLine();
        
//        var_dump('media:', $curLine);
        if ($curLine->startsWith('@media ')) {
            $mediaReq = new MediaRequest(null);
            $mediaReq->setRequest(substr($curLine->getLine(), 7));
            return $mediaReq;
        }
        
        return false;
    }
}
