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

namespace MSSLib\Essentials;

use MSSLib\Essentials\ParserContext;
use MSSLib\Essentials\IParser;
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
