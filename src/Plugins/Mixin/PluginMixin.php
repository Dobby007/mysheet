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

use MSSLib\Plugins\PluginBase;
use MSSLib\Structure\Declaration;
use MSSLib\Essentials\VariableScope;

/**
 * Description of Mixin
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class PluginMixin extends PluginBase {
    private $mixins = array();
    
    
    public function init() {
        self::getRootObj()->getHandlerFactory()
                          ->registerHandler('Declaration', 'renderCss', [$this, 'mixinHandler'])
                          ->registerHandler('Block', 'cssRenderingStarted', 
                                function() {
                                    $this->cleanMixins();
                                }
        );
        self::getRootObj()->getParser()->addParserExtension(new MixinParserExtension($this));
    }
    
    public function registerMixin(Mixin $mixin) {
        $this->mixins[$mixin->getName()] = $mixin;
//        var_dump($mixin);
    }
    
    public function getMixin($name) {
        return isset($this->mixins[$name]) ? $this->mixins[$name] : false;
    }
    
    public function isMixin($name) {
        return isset($this->mixins[$name]);
    }
    
    public function cleanMixins() {
        $this->mixins = [];
    }
    
    public function mixinHandler(&$handled, Declaration $rule, VariableScope $userRuleScope) {
        /* @var $mixin Mixin */
        $mixin = $this->getMixin($rule->getRuleName());
        if ($mixin) {
            $handled = true;
            $vs = new VariableScope();
            $vs->enableNumericVars(true);
            $vs->setMap($rule->getRuleValue()->getValue($userRuleScope, true));
            
            return $mixin->render($vs);
        }
        
    }
}
