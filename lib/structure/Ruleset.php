<?php

namespace MySheet\Structure;

require_once 'Block'. EXT;
require_once 'Selector'. EXT;
require_once 'Declaration'. EXT;

use MySheet\Structure\Selector;
use MySheet\Structure\Declaration;

/**
 * Description of Ruleset
 *
 * @author dobby007
 */
class Ruleset extends Block {
    private $selectors = array();
    private $declarations = array();
    
    public function __construct($parent) {
        parent::__construct($parent);
    }
    
    public function getSelectors() {
        return $this->selectors;
    }
    
    public function countSelectors() {
        return count($this->selectors);
    }

    public function addSelector(Selector $selector) {
        $this->selectors[] = $selector;
    }
    
    
    public function getDeclarations() {
        return $this->declarations;
    }
    
    public function countDeclarations() {
        return count($this->declarations);
    }

    public function addDeclaration(Declaration $declaration) {
        $this->declarations[] = $declaration;
    }
}
