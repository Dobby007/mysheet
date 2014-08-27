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

    private $selectors = array();
    private $declarations = array();

    public function __construct($parent) {
        parent::__construct($parent);
    }

    public function setParent($parent) {
        parent::setParent($parent);
        $this->parseSelectors();
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
    
    protected function parseSelectors() {
        foreach ($this->selectors as $selector) {
            $selector->parsePath();
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
            $declarations = StringHelper::parseSplittedString($declarations, ';', false);
//            $declarations = preg_split('/(?![\s;]+$);/', $declarations);
        }    
        if (is_array($declarations)) {
            foreach ($declarations as $declaration) {
                //ignore empty strings: they can appear if user typed semicolon several times
                if (!empty($declaration)) {
                    $this->addDeclaration($declaration);
                }
            }
        }
    }

    public function getParentPaths() {
        $parent = $this->getParent();
        do {
            if ($parent instanceof Ruleset) {
                $parent_paths = $parent->getCssPaths();
                if (!is_array($parent_paths)) {
                    $parent_paths = [(string) $parent_paths];
                }
                return $parent_paths;
            }
        } while ( $parent instanceof NodeBlock && ($parent = $parent->getParent()) );

        return [];
    }

    public function getCssPaths() {
        $parent_paths = $this->getParentPaths();
        $my_selectors = $this->getSelectors();
        $combined = [];

        array_walk($my_selectors, function(Selector $selector) use($parent_paths, &$combined) {
            $combined = array_merge($combined, $selector->isFullSelector() ?
                            $selector->getCssPathGroup()->getPaths() : Selector::unionSelectors($parent_paths, $selector)
            );
        });

        return $combined;
    }

    protected function compileRealCss(VariableScope $vars = null) {
        $lines = new StringBuilder();
        $selectors = $this->getCssPaths();
        $declarations = $this->getDeclarations();

        if (empty($selectors)) {
            throw new CompileException(null, 'NO_SELECTORS_FOUND');
        }

        //if there are no rules and no children return nothing
        if (empty($declarations) && empty($this->getChildren())) {
            return [];
        }

        $compiled_declarations = [];

        array_walk($declarations, function($decl) use (&$compiled_declarations) {
            $result = $decl->toRealCss();

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

        $lines->addLines(parent::compileRealCss());
//        ArrayHelper::concat($lines, parent::compileRealCss());


        return $lines;
    }

}
