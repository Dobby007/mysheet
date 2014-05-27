<?php

namespace MySheet\Structure;

use MySheet\Structure\Selector;
use MySheet\Structure\Declaration;
use MySheet\Structure\RuleGroup;
use MySheet\Helpers\ArrayHelper;

/**
 * Description of Ruleset
 *
 * @author dobby007
 */
class Ruleset extends Block {
    use \MySheet\Traits\RootClassTrait;
    
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

    public function addSelector($selector) {
        if (is_string($selector)) {
            $this->selectors[] = new Selector($selector, $this);
        }
    }
    
    public function addSelectors(array $selectors) {
        foreach ($selectors as $selector) {
            $this->addSelector($selector);
        }
    }
    
    public function getDeclarations() {
        return $this->declarations;
    }
    
    public function countDeclarations() {
        return count($this->declarations);
    }

    public function addDeclaration($declaration) {
        if (is_string($declaration)) {
            $this->declarations[] = (new Declaration($declaration))
                ->setRoot($this->getRoot());
        }
    }
    
    public function getParentPath() {
        $parent_paths = $this->getParent()->getCssPath();
        if (!is_array($parent_paths)) {
            $parent_paths = [(string) $parent_paths];
        }
        return $parent_paths;
    }
    
    public function getCssPath() {
        $parent_paths = $this->getParentPath();
        $my_selectors = $this->getSelectors();
        $combined = [];
        
        array_walk($my_selectors, function($selector) use($parent_paths, &$combined) {
            $combined = array_merge($combined, Selector::unionSelectors($parent_paths, $selector));
        });
        
        return $combined;
    }
    
    
    
    protected function compileRealCss() {
        $lines = [];
        $selectors = $this->getCssPath();
        $declarations = $this->getDeclarations();
        
        if (empty($selectors)) {
            //throw
        }
        
        //if there are no rules return nothing
        if (empty($declarations)) {
            return [];
        }
        
        $compiled_declarations = [];

        array_walk($declarations, function($decl) use (&$compiled_declarations) {
            $result = $decl->toRealCss();
//            var_dump(gettype($result), get_class($result));
            
            if ($result instanceof RuleGroup) {
                foreach ($result->getLines(': ') as $line) {
//                    var_dump($line);
                    $compiled_declarations[] = '    ' . (string)$line;
                }
            } else {
                $compiled_declarations[] = '    ' . (string)$result;
            }
        });
        
        
        ArrayHelper::concat($lines, implode(",\n", $selectors), '{', implode(";\n", $compiled_declarations), '}', parent::compileRealCss());
        
        return $lines;
    }
}
