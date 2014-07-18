<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Plugins\SelectorExtensions;

use MySheet\Plugins\PluginBase;
use MySheet\Essentials\VariableScope;
use MySheet\Helpers\StringHelper;
use MySheet\Structure\PathGroup;
use MySheet\Structure\Selector;


/**
 * Description of Mixin
 *
 * @author dobby007
 */
class PluginSelectorExtensions extends PluginBase {
    private $handlers = array('parent'/*, 'any'*/);
    
    
    public function init() {
        $this->getRoot()->getHandlerFactory()->registerHandler('Selector', 'cssSelectorParsing', function(&$handled, Selector $selector, PathGroup $pathGroup) {
            foreach ($this->handlers as $handler) {
                $method_name = 'parse' . ucfirst($handler) . 'Handler';
                $this->$method_name($selector, $pathGroup);
            }
        });
    }
    
    public function parseParentHandler(Selector $selector, PathGroup $pathGroup) {
        var_dump('parse parent');
        $newPaths = [];
        $parentPaths = $selector->getRuleset()->getParentPaths();
        foreach ($pathGroup->getPaths() as $path) {
            foreach ($parentPaths as $parentPath) {
                var_dump('PARENT PATH:' . $parentPath);
                if (empty($parentPath)) {
                    continue;
                }
                $path = str_replace('&', (string)$parentPath, $path, $count);
                if ($count > 0) {
                    $selector->setFullSelector(true);
                }
            }
            
            $newPaths[] = $path;
        }
        $pathGroup->setPaths($newPaths);
    }
    
    public function parseAnyHandler(Selector $selector, PathGroup $pathGroup) {
        var_dump('parse any');
        
        function findAnyMatches($path, $offset, &$position, &$patternLength) {
            $position = strpos($path, ':any', $offset);
            if ($position >= 0) {
                $enclosedWithBrackets = StringHelper::parseEnclosedString($string);
                if ($enclosedWithBrackets) {
                    $patternLength = $enclosedWithBrackets + 6;
                    $subSelectors = StringHelper::parseSplittedString(substr($enclosedWithBrackets, 1, -1), ',');
                    return $subSelectors;
                }
            }
            return false;
        }
        
        $newPaths = [];
        $parentPaths = $selector->getRuleset()->getParentPaths();
        
        $queue = new \SplQueue();
        foreach ($pathGroup->getPaths() as $path) {
            $offset = 0;
            while ($subSelectors = findAnyMatches($path, $offset, $pos, $wholeLength)) {
                $offset = $pos + $wholeLength;
                
            }
            
            $newPaths[] = $path;
        }
        $pathGroup->setPaths($newPaths);
    }
}
