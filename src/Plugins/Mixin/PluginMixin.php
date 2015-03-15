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
class PluginMixin extends PluginBase
{
    private $_registeredMixins = array();
    private $_systemMixins = array();
    
    protected $_enabledMixinSetClasses = array();
    
    
    public function init() {
        self::getRootObj()->getHandlerFactory()
                          ->registerHandler('Declaration', 'renderCss', [$this, 'mixinHandler'])
                          ->registerHandler('Block', 'cssRenderingStarted', 
                                function() {
                                    $this->cleanMixins();
                                }
        );
        self::getRootObj()->getParser()->addParserExtension(new MixinParserExtension($this));
        foreach ($this->_enabledMixinSetClasses as $mixinSetClass) {
            if (strpos($mixinSetClass, '\\') === false) {
                $mixinSetClass = ucfirst($mixinSetClass) . 'Set';
                $mixinSetClass = '\\MSSLib\\Plugins\\Mixin\\EmbeddedMixins\\' . $mixinSetClass;
            }
            
            if (class_exists($mixinSetClass)) {
                $this->registerMixinSet(new $mixinSetClass);
            }
        }
    }
    
    public function registerMixin(Mixin $mixin) {
        $this->_registeredMixins[$mixin->getName()] = $mixin;
    }
    
    public function registerMixinSet(MixinSet $mixinSet) {
        $mixins = $mixinSet->getMixinsInSet();
        $this->_systemMixins = array_unique(array_merge($this->_systemMixins, $mixins), SORT_REGULAR);
    }
    
    public function getMixin($name) {
        if (isset($this->_registeredMixins[$name])) {
            return $this->_registeredMixins[$name];
        }
        
        $mixin = $this->createSystemMixin($name);
        if ($mixin) {
            $this->registerMixin($mixin);
            return $this->_registeredMixins[$name];
        }
        return false;
    }
    
    private function createSystemMixin($name) {
        if (isset($this->_systemMixins[$name]) && is_callable($this->_systemMixins[$name])) {
            return $this->_systemMixins[$name]();
        }
        return false;
    }
    
    public function isMixin($name) {
        return isset($this->_registeredMixins[$name]);
    }
    
    public function cleanMixins() {
        $this->_registeredMixins = [];
    }
    
    public function setMixinSets($mixinSets) {
        if (is_string($mixinSets)) {
            $mixinSets = SettingsHelper::convertPriorityStringToArray($mixinSets);
        }
        if (is_array($mixinSets)) {
            $this->_enabledMixinSetClasses = $mixinSets;
        }
    }
    
    public function mixinHandler(&$handled, Declaration $rule, VariableScope $userRuleScope) {
        /* @var $mixin Mixin */
        $mixin = $this->getMixin($rule->getRuleName());
        if ($mixin) {
            $handled = true;
            $vs = new VariableScope();
            $vs->enableNumericVars(true);
            $vs->setMap($rule->getRuleValue()->getCompiledParams($userRuleScope));
            return $mixin->render($vs);
        }
        
    }
}
