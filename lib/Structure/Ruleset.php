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
class Ruleset extends NodeBlock {
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
            $this->declarations[] = (new Declaration($declaration));
        }
    }
    
    public function addDeclarations($declarations) {
        if (is_string($declarations)) {
            $declarations = preg_split('/(?![\s;]+$);/', $declarations);
        }
        
        if (is_array($declarations)) {
            foreach ($declarations as $declaration) {
                $this->addDeclaration($declaration);
            }
        }
    }
    
    public function getParentPath() {
        $parent = $this->getParent();
        do {
            if ($parent instanceof Ruleset) {
                $parent_paths = $parent->getCssPath();
                if (!is_array($parent_paths)) {
                    $parent_paths = [(string) $parent_paths];
                }
                return $parent_paths;
            }
        } while ($parent = $parent->getParent());
        
        return [];
        
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
    
    
    
    protected function compileRealCss(VariableScope $vars = null) {
        $lines = [];
        $selectors = $this->getCssPath();
        $declarations = $this->getDeclarations();
        
        if (empty($selectors)) {
            //throw
        }
        
        //if there are no rules and no children return nothing
        if (empty($declarations) && empty($this->getChildren())) {
            return [];
        }
        
        $compiled_declarations = [];
        $prefixRule = $this->getSetting('cssRenderer.prefixRule', '    ');
        
        array_walk($declarations, function($decl) use (&$compiled_declarations) {
            $result = $decl->toRealCss();
//            var_dump(gettype($result), get_class($result));
            
            if ($result instanceof RuleGroup) {
                foreach ($result->getLines(': ') as $line) {
//                    var_dump($line);
                    $compiled_declarations[] = (string)$line;
                }
            } else {
                $compiled_declarations[] = (string)$result;
            }
        });
        
        //nothing to render if there are no declarations
        if (!empty($compiled_declarations)) {
            $sepSelectors = $this->getSetting('cssRenderer.sepSelectors', ',');
            
            $selectors = ArrayHelper::processLines($selectors, '', '', $sepSelectors);
            
            if ($this->getSetting('cssRenderer.lfAfterSelector', true) == false) {
                $selectors = implode('', $selectors);
            }
            
            $compiled_declarations = ArrayHelper::processLines($compiled_declarations, $prefixRule, '', ';');
            ArrayHelper::concat($lines, $selectors, '{', $compiled_declarations, '}');
        }
            
        ArrayHelper::concat($lines, parent::compileRealCss());
        
        
        return $lines;
    }
}
