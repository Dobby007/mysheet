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

namespace MSSLib\Traits;

use MSSLib\Plugins\PluginBase;

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
    protected $plugin = null;
    
    /**
     * @return PluginBase
     */
    public function getPlugin() {
        return $this->plugin;
    }

    public function setPlugin(PluginBase $plugin) {
        $this->plugin = $plugin;
    }


}
