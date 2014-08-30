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
    
    private $path, $cssPathGroup;
    private $ruleset;
    private $fullSelector;
    
    public function __construct($path, $ruleset) {
        $this->setRuleset($ruleset);
        $this->setPath($path);
    }
    
    /**
     * Return parent ruleset which selector belongs to
     * @return Ruleset
     */
    public function getRuleset() {
        return $this->ruleset;
    }
    
    /**
     * Returns input selector as a group of mss selectors
     * @return PathGroup
     */
    public function getPathGroup() {
        $pg = new PathGroup();
        $pg->addPath($this->path);
        return $pg;
    }
    
    /**
     * Returns parsed input selector as a group of css selectors
     * @return PathGroup
     */
    public function getCssPathGroup() {
        $pg = clone $this->cssPathGroup;
        $this->cssSelectorRenderingEvent($this, $pg);
        return $pg;
    }

    public function parsePath() {
        $pg = $this->getPathGroup();
        $this->cssSelectorParsingEvent($this, $pg);
        $this->cssPathGroup = $pg;
    }
    
    public function setPath($path) {
        $path = trim($path);
        $right_selector = self::canBeSelector($path);
        
        if ($right_selector) {
            $path = preg_replace(['/\s+/'/*, '/\s+:\s*(\S+)/'*/], [' '/*, ':$1'*/], $path);
            $this->path = $path;
        } else {
            throw new ParseException(null, 'BAD_SELECTOR', [$path]);
        }
    }

    public function setRuleset(Ruleset $ruleset) {
        $this->ruleset = $ruleset;
    }
    
    /**
     * Determines whether selector is full or not
     * @return bool
     */
    public function isFullSelector() {
        return $this->fullSelector;
    }

    /**
     * Sets if selector is full or not. If selector is full it does not need to be preceeded by selector of the parent ruleset
     * @param bool $fullSelector
     */
    public function setFullSelector($fullSelector) {
        $this->fullSelector = !!$fullSelector;
    }
    
    public static function unionSelectors(array $parent_selectors, Selector $selector) {
        if (empty($parent_selectors)) {
            $parent_selectors[] = '';
        }
        
        $combined = [];
        foreach ($parent_selectors as $psel) {
            foreach ($selector->getCssPathGroup()->getPaths() as $path) {
                $combined[] = (empty($psel) ? '' : $psel . ' ') . $path;
            }
        }
        return $combined;
    }
    
    public static function canBeSelector($string, &$matches = null) {
        return !!preg_match('/^[a-z0-9\[\].,\s!:\'"=^$#()*&]+$/i', $string, $matches);
    }
}
