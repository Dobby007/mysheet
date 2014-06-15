<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Traits;

use MySheet\MySheet;
use MySheet\Tools\MSSettings;

/**
 * Description of RootClassTrait
 *
 * @author dobby007
 */
trait RootClassTrait {
    
    /**
     * @return MySheet Instance of MySheet class
     */
    public function getRoot() {
        return MySheet::Instance();
    }
    
    /**
     * @return MSSettings
     */
    public function getSettings() {
        return $this->getRoot()->getSettings();
    }
    
    /**
     * @return mixed
     */
    public function getSetting($name, $default = null) {
        return $this->getRoot()->getSettings()->get($name, $default);
    }
    
}
