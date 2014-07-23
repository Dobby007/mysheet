<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Traits;

use MySheet\Plugins\PluginBase;

/**
 * Description of PluginClassTrait
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
trait PluginClassTrait {
    /**
     *
     * @var PluginBase
     */
    private $plugin = null;
    
    /**
     * @return PluginBase
     */
    protected function getPlugin() {
        return $this->plugin;
    }

    protected function setPlugin(PluginBase $plugin) {
        $this->plugin = $plugin;
    }


}
