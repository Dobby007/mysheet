<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Traits;

use MySheet\MySheet;

/**
 * Description of RootClassTrait
 *
 * @author dobby007
 */
trait RootClassTrait {
    protected $root = null;
    
    /**
     * @return this
     */
    public function setRoot(MySheet $root) {
        $this->root = $root;
        return $this;
    }
    
    /**
     * @return MySheet Instance of MySheet class
     */
    public function getRoot() {
        return $this->root;
    }
    
    public function getSettings() {
        return $this->getRoot()->getSettings();
    }
    
    public function isRootSet() {
        return $this->root !== null;
    }
}
