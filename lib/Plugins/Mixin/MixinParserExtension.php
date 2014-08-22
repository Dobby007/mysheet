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
use MySheet\Traits\PluginClassTrait;

/**
 * Description of MixinParserExtension
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class MixinParserExtension extends ParserExtension {
    use PluginClassTrait;
    /**
     * Reference to plugin object
     * @var PluginMixin
     */
    private $plugin;
    
    public function __construct(PluginMixin $plugin) {
        $this->setPlugin($plugin);
    }
    
    public function parse() {
        $context = $this->getContext();
        $firstLine = $curLine = $context->curLine();
        if (!$firstLine->startsWith('@mixin ')) {
            return false;
        }
        if ($firstLine->getLevel() !== 1) {
            throw new ParseException(ErrorTable::E_BAD_INDENTATION);
        }
        
        if (!preg_match('/^@mixin\s+([-a-z][a-z\d_-]*)\s*\((.*)\)$/', $firstLine->getLine(), $mixin_decl)) {
            throw new ParseException(ErrorTable::E_BAD_INDENTATION);
        }
        
        preg_match_all('/(?:[a-z_][a-z0-9_]*)/i', $mixin_decl[2], $mixin_locals, PREG_PATTERN_ORDER);
        
        $mixin = new Mixin($this->plugin, $mixin_decl[1], $mixin_locals[0]);
        
        while ($curLine = $context->nextLine(true)) {
            if ($curLine->getLevel() > $firstLine->getLevel()) {
                $mixin->addDeclarations($curLine->getLine());
            } else break;
        }
        $context->prevLine(true);
        return $mixin;
    }
}
