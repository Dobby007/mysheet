<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Essentials;

use MySheet\Tools\IParser;
use MySheet\Traits\ParserLinesTrait;
use MySheet\Traits\ParserCursorStateTrait;

/**
 * Description of ParserContext
 *
 * @author dobby007
 */
class ParserContext {
    use ParserLinesTrait, ParserCursorStateTrait;
    
    private $parser;
    
    public function __construct(IParser $parser, array $lines, $curLine) {
        $this->lines = $lines;
        $this->setLineCursor($curLine);
        $this->setParser($parser);
    }
    
    /**
     * 
     * @return IParser
     */
    public function getParser() {
        return $this->parser;
    }
    
    public function setParser(IParser $parser) {
        $this->parser = $parser;
    }
    
    public function parse(ParserExtension $extension) {
        $extension->setContext($this);
        return $extension->parse();
    }
}
