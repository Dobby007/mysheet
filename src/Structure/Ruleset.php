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
use MSSLib\Tools\Finders\RulesetFinder;
use MSSLib\Essentials\BlockInterfaces\IMayContainRuleset;
/**
 * Class that represents CSS ruleset that consists of selectors (Selector) and declarations (Declaration)
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class Ruleset extends NodeBlock implements IMayContainRuleset {

    private $_selectors = array();
    private $_declarations = array();
    protected $_parentRuleset = null;
    
    public function __construct($parent) {
        parent::__construct($parent);
    }

    public function setParent($parent) {
        parent::setParent($parent);
        $this->findParentRuleset();
        $this->parseSelectors();
    }
    
    /**
     * Finds and reminds nearest parent ruleset
     * @return null
     */
    protected function findParentRuleset() {
        $parent = $this->getParent();
        if (!($parent instanceof NodeBlock)) {
            return;
        }
        
        do {
            if ($parent instanceof Ruleset) {
                $this->_parentRuleset = $parent;
                break;
            }
        } while ( ($parent = $parent->getParent()) && $parent instanceof NodeBlock );
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
        } else if ($selector instanceof Selector) {
            $this->_selectors[] = $selector->setRuleset($this);
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
        } else if ($declaration instanceof Declaration) {
            $this->_declarations[] = $declaration;
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
    
    public function getMatchedSelectors($selectorsToMatch, $cutOffSelectors = true, $flags = RulesetFinder::DEFAULT_FIND_FLAGS) {
        $selectors = [];
        if (!is_array($selectorsToMatch)) {
            $selectorsToMatch = (string)$selectorsToMatch;
        }
        if ($flags & RulesetFinder::FIND_ONLY_MSS_SELECTORS) {
            $selectors = array_map(function (Selector $item) {
                return $item->getMssPath();
            }, $this->getSelectors());
        }
        
        if ($flags & RulesetFinder::FIND_ONLY_CSS_SELECTORS) {
            $selectors = array_merge($selectors, $this->getFullCssSelectors());
        }

        $selectors = array_unique($selectors);
        $matchedSelectors = [];
        foreach ($selectors as $selector) {
            $selectorLen = strlen($selector);
            foreach ($selectorsToMatch as $selectorToMatch) {
                $nextSymbol = substr($selectorToMatch, $selectorLen, 1);
                if (strncmp($selector, $selectorToMatch, $selectorLen) === 0 && (ctype_space($nextSymbol) || empty($nextSymbol))) {
                    $matchedSelectors[] = $cutOffSelectors ? (!($end = substr($selectorToMatch, $selectorLen)) && !$end ? '' : trim($end)) : $selector;
                }
            }
        }
        return $matchedSelectors;
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
