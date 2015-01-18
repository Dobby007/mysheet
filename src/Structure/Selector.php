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

use MSSLib\Error\ParseException;
use MSSLib\Error\ErrorTable;
use MSSLib\Structure\Ruleset;
use MSSLib\Structure\PathGroup;
use MSSLib\Traits\RootClassTrait;
use MSSLib\Traits\HandlerCallTrait;


/**
 * Class that represents a single MSS selector that can be rendered to CSS one(s)
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class Selector {
    use RootClassTrait, HandlerCallTrait;
    
    private $_mssPath, $_cssPathGroup;
    private $_ruleset;
    private $_isFullSelector;
    private $_isParsed = false;
    
    public function __construct($mssPath, $ruleset) {
        $this->setRuleset($ruleset);
        $this->setMssPath($mssPath);
    }
    
    /**
     * Return parent ruleset which selector belongs to
     * @return Ruleset
     */
    public function getRuleset() {
        return $this->_ruleset;
    }
    
    /**
     * Creates a group of selectors and add source MSS selector to it
     * @return PathGroup
     */
    protected function createPathGroup() {
        $pg = new PathGroup();
        $pg->addPath($this->_mssPath);
        return $pg;
    }
    
    /**
     * Returns parsed input selector as a group of css selectors
     * @return PathGroup
     */
    public function getCssPathGroup() {
        return $this->_cssPathGroup;
    }

    public function parse() {
        $pg = $this->createPathGroup();
        $this->cssSelectorParsingEvent($this, $pg);
        $this->_cssPathGroup = $pg;
        $this->_isParsed = true;
        $this->cssSelectorParsedEvent($this, $pg);
        $this->fulfilPaths();
    }
    
    protected function fulfilPaths() {
        if (!$this->isFullSelector() && $this->getRuleset()) {
            $paths = Selector::unionSelectorWithParents($this->getRuleset()->getParentPaths(), $this);
            $this->_cssPathGroup->setPaths($paths);
        }
    }
    
    public function isParsed() {
        return $this->_isParsed;
    }
    
    public function getMssPath() {
        return $this->_mssPath;
    }
    
    public function setMssPath($path) {
        $path = trim($path);
        $right_selector = self::canBeSelector($path);
        
        if ($right_selector) {
            $path = preg_replace(['/\s+/'/*, '/\s+:\s*(\S+)/'*/], [' '/*, ':$1'*/], $path);
            $this->_mssPath = $path;
            $this->_isParsed = false;
        } else {
            throw new ParseException(null, 'BAD_SELECTOR', [$path]);
        }
        return $this;
    }

    public function setRuleset(Ruleset $ruleset) {
        $this->_ruleset = $ruleset;
        return $this;
    }
    
    /**
     * Determines whether selector is full or not
     * Full selectors ore the ones that do not need to be merged with their parent selectors (for example selectors that contain & symbol)
     * @return bool
     */
    public function isFullSelector() {
        return $this->_isFullSelector;
    }

    /**
     * Sets if selector is full or not. If selector is full it does not need to be preceeded by selector of the parent ruleset
     * @param bool $fullSelector
     */
    public function setFullSelector($fullSelector) {
        $this->_isFullSelector = !!$fullSelector;
        return $this;
    }
    
    public static function unionSelectorWithParents(array $parentSelectors, Selector $selector) {
        if (empty($parentSelectors)) {
            $parentSelectors[] = '';
        }
        
        $combined = [];
        $cssPathGroup = $selector->getCssPathGroup();
        foreach ($parentSelectors as $psel) {
            foreach ($cssPathGroup->getPaths() as $path) {
                $combined[] = (empty($psel) ? '' : $psel . ' ') . $path;
            }
        }
        return $combined;
    }
    
    public static function canBeSelector($string, &$matches = null) {
        return !!preg_match('/^[a-z0-9\[\]\-_.,\s!:\'"=>$#()\*&]+$/i', $string, $matches);
    }
}
