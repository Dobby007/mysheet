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
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
abstract class PluginBase {
    use \MySheet\Traits\RootClassTrait,
        \MySheet\Traits\MagicPropsTrait;
    
    public function __construct() { }

    
    abstract public function init();
}
