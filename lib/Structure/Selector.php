<?php

namespace MySheet\Structure;

use MySheet\Error\ParseException;
use MySheet\Error\ErrorTable;
use MySheet\Structure\Ruleset;
use MySheet\Structure\PathGroup;


//Workaround: autoload is unregistered before PathGroup is loaded (I don't know why it worked before today)
require_once 'PathGroup' . EXT;


/**
 * Description of Selector
 *
 * @author dobby007
 */
class Selector {
    private $path;
    private $ruleset;
    
    public function __construct($path, $ruleset) {
        $this->setPath($path);
        $this->setRuleset($ruleset);
    }
    
    public function getRuleset() {
        return $this->ruleset;
    }
    
    public function getPath() {
        $pg = new PathGroup();
        $pg->addPath($this->path);
        return $pg;
    }

    public function setPath($path) {
        $path = trim($path);
        $right_selector = self::canBeSelector($path);
        
        if ($right_selector) {
            $this->path = preg_replace(['/\s+/', '/\s+:\s*(\S+)/'], [' ', ':$1'], $path) ;
        } else {
            throw new ParseException(ErrorTable::E_BAD_SELECTOR);
        }
    }

    public function setRuleset(Ruleset $ruleset) {
        $this->ruleset = $ruleset;
    }
    
    public function __toString() {
        return (string) $this->getPath();
    }
    
    public static function unionSelectors(array $parent_selectors, $selector) {
        if (count($parent_selectors) === 0) {
            $parent_selectors[] = '';
        }
        
        $combined = [];
        foreach ($parent_selectors as $psel) {
            $combined[] = (empty($psel) ? '' : $psel . ' ') . (string) $selector; 
        }
        return $combined;
    }
    
    public static function canBeSelector($string, &$matches = null) {
        return !!preg_match('/^[a-z0-9\[\].\s!:\'"=^$#()*]+$/i', $string, $matches);
    }
}
