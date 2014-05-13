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
    public function init() {
        $this->getRoot()->getHandlerFactory()->registerHandler('Declaration', 'renderCss', [$this, 'mixinHandler']);
    }
    
    public function isMixin($name) {
        return true;
    }
    
    public function mixinHandler(&$handled, $ruleName) {
        if ($ruleName === 'color')
            $handled = true;
        return 'handled!!!!';
    }
}
