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

namespace MSSLib\Structure;

use MSSLib\Structure\Selector;
use MSSLib\Structure\Declaration;
use MSSLib\Structure\RuleGroup;
use MSSLib\Helpers\ArrayHelper;
use MSSLib\Essentials\StringBuilder;
use MSSLib\Essentials\VariableScope;
use MSSLib\Helpers\StringHelper;
use MSSLib\Error\CompileException;

/**
 * Class that represents CSS rule set that consists of selectors (Selector) and declarations (Declaration)
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class Ruleset extends NodeBlock {

    private $_selectors = array();
    private $_declarations = array();
    private $_parentRuleset = null;
    
    public function __construct($parent) {
        parent::__construct($parent);
    }

    public function setParent($parent) {
        parent::setParent($parent);
        while ( $parent instanceof NodeBlock && ($parent = $parent->getParent()) ) {
            if ($parent instanceof Ruleset) {
                $this->_parentRuleset = $parent;
            }
        }
        $this->parseSelectors();
    }
    
    /**
     * Array of selectors related to this ruleset
     * @return Selector[]
     */
    public function getSelectors() {
        return $this->_selectors;
    }

    public function countSelectors() {
        return count($this->_selectors);
    }

    public function addSelector($selector) {
        if (is_string($selector)) {
            $this->_selectors[] = new Selector($selector, $this);
        }
    }

    public function addSelectors(array $selectors) {
        foreach ($selectors as $selector) {
            $this->addSelector($selector);
        }
    }
    
    protected function parseSelectors() {
        foreach ($this->_selectors as $selector) {
            $selector->parse();
        }
    }

    public function getDeclarations() {
        return $this->_declarations;
    }

    public function countDeclarations() {
        return count($this->_declarations);
    }

    public function addDeclaration($declaration) {
        if (is_string($declaration)) {
            $this->_declarations[] = (new Declaration($declaration));
        }
    }

    public function addDeclarations($declarations) {
        if (is_string($declarations)) {
            $declarations = StringHelper::parseSplittedString($declarations, ';', false);
//            $declarations = preg_split('/(?![\s;]+$);/', $declarations);
        }    
        if (is_array($declarations)) {
            foreach ($declarations as $declaration) {
                // ignore empty strings: they can appear if user typed semicolon several times
                if (!empty($declaration)) {
                    $this->addDeclaration($declaration);
                }
            }
        }
    }

    public function getParentPaths() {
        if ($this->_parentRuleset instanceof Ruleset) {
            $parent_paths = $this->_parentRuleset->getFullCssSelectors();
            if (!is_array($parent_paths)) {
                $parent_paths = [(string)$parent_paths];
            }
            return $parent_paths;
        }
        return [];
    }

    public function getFullCssSelectors() {
        $parentSelectors = $this->getParentPaths();
        $mySelectors = $this->getSelectors();
        $combined = [];

        foreach ($mySelectors as $selector) {
            // we consider full selectors here because they do not need to be merged with their parent
            $combined = array_merge($combined, $selector->getCssPathGroup()->getPaths());
        }

        return $combined;
    }

    protected function compileRealCss(VariableScope $vars) {
        $lines = new StringBuilder();
        $selectors = $this->getFullCssSelectors();
        $declarations = $this->getDeclarations();

        if (empty($selectors)) {
            throw new CompileException(null, 'NO_SELECTORS_FOUND');
        }

        // if there are no rules and no children return nothing
        if (empty($declarations) && empty($this->getChildren())) {
            return [];
        }

        $compiled_declarations = [];

        array_walk($declarations, function(Declaration $decl) use (&$compiled_declarations, $vars) {
            if (!$decl->getRuleEnabled()) {
                return;
            }
            
            $result = $decl->toRealCss($vars);

            if ($result instanceof RuleGroup) {
                foreach ($result->getLines(': ') as $line) {
                    $compiled_declarations[] = (string) $line;
                }
            } else {
                $compiled_declarations[] = (string) $result;
            }
        });

        //nothing to render if there are no declarations
        if (!empty($compiled_declarations)) {
            $selectors = implode($this->getSetting('cssRenderer.sepSelectors', ', '), $selectors);
            $lines->addLine($selectors);

            $compiled_declarations = ArrayHelper::implodeLines(
                            $compiled_declarations, $this->getSetting('cssRenderer.prefixRule', '    '), $this->getSetting('cssRenderer.suffixRule', ''), $this->getSetting('cssRenderer.sepRules', ";\n")
            );

            $lines->appendText(
                            $this->getSetting('cssRenderer.prefixOCB', ' ') .
                            '{' .
                            $this->getSetting('cssRenderer.suffixOCB', "\n")
                    )
                    ->appendText(
                            $this->getSetting('cssRenderer.prefixDeclBlock', "") .
                            $compiled_declarations .
                            $this->getSetting('cssRenderer.suffixDeclBlock', "")
                    )
                    ->appendText(
                            $this->getSetting('cssRenderer.prefixCCB', "\n") .
                            '}' .
                            $this->getSetting('cssRenderer.suffixCCB', "\n")
            );
//            ArrayHelper::concat($lines, $selectors, '{', $compiled_declarations, '}');
        }

        $lines->addLines(parent::compileRealCss($vars));


        return $lines;
    }

}
