<?php

namespace MySheet\Structure;

use MySheet\Error\ParseException;
use MySheet\Error\ErrorTable;
use MySheet\Structure\Ruleset;
use MySheet\Structure\PathGroup;
use MySheet\Traits\RootClassTrait;
use MySheet\Traits\HandlerCallTrait;


/**
 * Description of Selector
 *
 * @author dobby007
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
     * 
     * @return Ruleset
     */
    public function getRuleset() {
        return $this->ruleset;
    }
    
    public function getPathGroup() {
        $pg = new PathGroup();
        $pg->addPath($this->path);
        return $pg;
    }
    
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
            $path = preg_replace(['/\s+/', '/\s+:\s*(\S+)/'], [' ', ':$1'], $path);
            $this->path = $path;
        } else {
            throw new ParseException(ErrorTable::E_BAD_SELECTOR, [$path]);
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
     * Sets if selector is full or not. If selector is full it does not need to be prefixed with parent selector
     * @param bool $fullSelector
     */
    public function setFullSelector($fullSelector) {
        $this->fullSelector = !!$fullSelector;
    }
    
    public function __toString() {
        return (string) $this->getCssPathGroup();
    }
    
    public static function unionSelectors(array $parent_selectors, Selector $selector) {
        if (empty($parent_selectors)) {
            $parent_selectors[] = '';
        }
        
        $combined = [];
        foreach ($parent_selectors as $psel) {
            $combined[] = (empty($psel) ? '' : $psel . ' ') . $selector->getCssPathGroup(); 
        }
        return $combined;
    }
    
    public static function canBeSelector($string, &$matches = null) {
        return !!preg_match('/^[a-z0-9\[\].\s!:\'"=^$#()*&]+$/i', $string, $matches);
    }
}
