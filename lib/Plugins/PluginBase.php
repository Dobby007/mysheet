<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Plugins;

/**
 * Description of PluginBase
 *
 * @author dobby007
 */
abstract class PluginBase {
    use \MySheet\Traits\RootClassTrait;
    
    public function __construct($rootInstance) {
        $this->setRoot($rootInstance);
    }

    
    abstract public function init();
}
