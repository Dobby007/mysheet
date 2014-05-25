<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Plugins\Mixin;

use MySheet\Plugins\PluginBase;

/**
 * Description of Mixin
 *
 * @author dobby007
 */
class PluginMixin extends PluginBase {
    private $mixins = array();
    
    public function init() {
        $this->getRoot()->getHandlerFactory()->registerHandler('Declaration', 'renderCss', [$this, 'mixinHandler']);
        $this->getRoot()->getParser()->addParserExtension(new MixinParserExtension($this));
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
    
    public function mixinHandler(&$handled, $ruleName) {
        $mixin = $this->getMixin($ruleName);
        if ($mixin)
            $handled = true;
        return 'handled!!!!';
    }
}
