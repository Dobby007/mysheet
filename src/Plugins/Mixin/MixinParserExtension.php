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

namespace MSSLib\Plugins\Mixin;

use MSSLib\Essentials\ParserExtension;
use MSSLib\Error\ParseException;
use MSSLib\Error\ErrorTable;
use MSSLib\Traits\PluginClassTrait;

/**
 * Description of MixinParserExtension
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class MixinParserExtension extends ParserExtension {
    use PluginClassTrait;
    
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
        
        $mixin = new Mixin(null);
        $mixin->setName($mixin_decl[1])->setLocals($mixin_locals[0])->setPlugin($this->getPlugin());
        
        while ($curLine = $context->nextLine(true)) {
            if ($curLine->getLevel() > $firstLine->getLevel()) {
                $mixin->addDeclarations($curLine->getLine());
            } else break;
        }
        return $mixin;
    }
}
