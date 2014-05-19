<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Plugins\Mixin;

use MySheet\Essentials\ParserExtension;
use MySheet\Error\ParseException;
use MySheet\Error\ErrorTable;

/**
 * Description of MixinParserExtension
 *
 * @author dobby007
 */
class MixinParserExtension extends ParserExtension {
    /**
     * Reference to plugin object
     * @var PluginMixin
     */
    private $plugin;
    
    public function __construct(PluginMixin $plugin) {
        $this->plugin = $plugin;
    }
    
    public function parse() {
        $context = $this->getContext();
        $firstLine = $curLine = $context->curline();
        
        if (substr($firstLine[1], 0, 6) !== '@mixin')
            return false;
        
        if ($firstLine[0] !== 0)
            throw new ParseException(ErrorTable::E_BAD_INDENTATION);
        
        if (!preg_match('/^@mixin\s+([-a-z][a-z\d_-]*)\s*\(\s*\)$/', $firstLine[1], $mixin_decl))
            throw new ParseException(ErrorTable::E_BAD_INDENTATION);
        
        
        $mixin = new Mixin($mixin_decl[1]);
        
        while ($curLine = $context->nextline()) {
            if ($curLine[0] > $firstLine[0]) {
                $mixin->addDeclaration($curLine[1]);
            } else break;
        }
        
        $this->plugin->registerMixin($mixin);
        
        return null;
    }
}
