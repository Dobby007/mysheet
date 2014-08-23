<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MSSLib\Plugins;
use MSSLib\Traits\RootClassTrait;
use MSSLib\Traits\MagicPropsTrait;
/**
 * Description of PluginBase
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
abstract class PluginBase {
    use RootClassTrait,
        MagicPropsTrait;
    
    public function __construct() { }

    
    abstract public function init();
}
