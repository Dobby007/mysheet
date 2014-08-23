<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MSSLib\Essentials;

use MSSLib\Essentials\ParserContext;
use MSSLib\Tools\IParser;
use MSSLib\Traits\RootClassTrait;

/**
 * Description of ParserExtension
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
abstract class ParserExtension 
{
    use RootClassTrait;
    
    private $context = null;
    
    /**
     * 
     * @return IParser
     */
    public function getParser() {
        return $this->getContext()->getParser();
    }
    
    /**
     * 
     * @return ParserContext
     */
    public function getContext() {
        return $this->context;
    }

    public function setContext(ParserContext $context) {
        $this->context = $context;
    }
    
    public abstract function parse();
}
