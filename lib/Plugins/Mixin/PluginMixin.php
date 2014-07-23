<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Plugins\Mixin;

use MySheet\Plugins\PluginBase;
use MySheet\Structure\Declaration;
use MySheet\Essentials\VariableScope;

/**
 * Description of Mixin
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class PluginMixin extends PluginBase {
    private $mixins = array();
    
    
    public function init() {
        $this->getRoot()->getHandlerFactory()->registerHandler('Declaration', 'renderCss', [$this, 'mixinHandler']);
        $this->getRoot()->getParser()->addParserExtension(new MixinParserExtension($this));
        $this->getRoot()->getHandlerFactory()->registerHandler('Block', 'cssRenderingStarted', function() {
            $this->cleanMixins();
        });
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
    
    public function mixinHandler(&$handled, Declaration $rule, VariableScope $userRuleScope = null) {
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
