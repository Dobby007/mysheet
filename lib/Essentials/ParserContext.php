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
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class ParserContext {
    use ParserLinesTrait, ParserCursorStateTrait;
    
    /**
     * @var IParser 
     */
    private $parser;
    
    /**
     * @var int
     */
    private $lineIndex;
    
    /**
     * @var SourceBlock
     */
    private $sourceBlock;
    
    public function __construct(IParser $parser, array $blockTree, $curLine) {
        $this->blockTree = $blockTree;
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
    
    public function nextLine() {
        return $this->curLine();
    }
    
    public function prevLine() {
        return $this->curLine();
    }
    
    public function curLine() {
        return $this->curSourceBlock()->getLine($this->lineIndex);
    }
    
    /**
     * Function is used to return the source block where the cursor is set
     * @return SourceBlock
     */
    public function curSourceBlock() {
        return $this->sourceBlock;
    }
    
    public function getCurrentLineIndex() {
        return $this->lineIndex;
    }
    
    public function getLine($offset) {
        $mySourceBlock = $this->sourceBlock;
        $myLineIndex = $this->lineIndex;
        
        while ($offset !== 0) {
            if ($myLineIndex < $mySourceBlock->countLines() - 1) {
                $myLineIndex++;
            } else {
                if ($msb = $mySourceBlock->getChildBlock($offset > 0 ? 0 : -1)) {
                    $mySourceBlock = $msb;
                } else if ($mySourceBlock) {
                    
                }
                
                
            }
            if ($offset > 0) {
                $offset--;
            } else {
                $offset++;
            }
        }
        
    }
    
    public function parse(ParserExtension $extension) {
        $extension->setContext($this);
        return $extension->parse();
    }
}
