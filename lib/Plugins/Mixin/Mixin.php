<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Plugins\Mixin;

use MySheet\Structure\Declaration;

/**
 * Description of Mixin
 *
 * @author dobby007
 */
class Mixin {
    
    private $declarations = array();
    private $name;
    
    public function __construct($name) {
        $this->setName($name);
    }
    
    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }
    
    public function getDeclarations() {
        return $this->declarations;
    }
    
    public function countDeclarations() {
        return count($this->declarations);
    }
    
    public function addDeclaration($declaration) {
        if (is_string($declaration)) {
            $this->declarations[] = new Declaration($declaration);
        }
    }
    
}
