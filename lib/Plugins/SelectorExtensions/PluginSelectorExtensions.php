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
    private $handlers = array('parent', 'any');
    
    
    public function init() {
        $this->getRoot()->getHandlerFactory()->registerHandler('Selector', 'cssSelectorParsing', function(&$handled, Selector $selector, PathGroup $pathGroup) {
            foreach ($this->handlers as $handler) {
                $method_name = 'parse' . ucfirst($handler) . 'Handler';
                $this->$method_name($selector, $pathGroup);
            }
        });
    }
    
    public function parseParentHandler(Selector $selector, PathGroup $pathGroup) {
        $newPaths = [];
        $parentPaths = $selector->getRuleset()->getParentPaths();
//        var_dump('parent paths:', $parentPaths);
        
        if (empty($parentPaths)) {
            $parentPaths = [''];
        }
        
        foreach ($pathGroup->getPaths() as $path) {
            foreach ($parentPaths as $parentPath) {
                $newPath = str_replace('&', (string)$parentPath, $path, $count);
//                var_dump('PARENT PATH:' . $parentPath . ', path=' . $newPath);
                if ($count > 0) {
                    $selector->setFullSelector(true);
                    $newPaths[] = $newPath;
                }
            }
            if (empty($newPaths)) {
                $newPaths[] = $path;
            }
        }
        $pathGroup->setPaths($newPaths);
    }
    
    public function parseAnyHandler(Selector $selector, PathGroup $pathGroup) {
        $findAnyMatches = function ($path, &$position, &$pattern) {
            $position = strpos($path, ':any');
            if ($position !== false) {
                $startPart = substr($path, $position);
                $partCopy = $startPart;
                $function = StringHelper::parseFunction($startPart);
                if ($function !== false) {
                    $pattern = substr($path, $position, strlen($partCopy) - strlen($startPart));
                    return $function['arguments'];
                }
                
            }
            return false;
        };
        
        $newPaths = [];
        $parentPaths = $selector->getRuleset()->getParentPaths();
        
        $queue = new \SplQueue();
        foreach ($pathGroup->getPaths() as $path) {
            $queue->enqueue($path);
            do {
                $path = $queue->dequeue();
                $subSelectors = $findAnyMatches($path, $pos, $pattern);
                $replaces = [];
                if ($subSelectors === false) {
                    $newPaths[] = $path;
                } else {
                    $replaces = StringHelper::replaceSubstring($pattern, $subSelectors, $path);
                    foreach ($replaces as $replace) {
                        $queue->enqueue($replace);
                    }
                }
            } while (!$queue->isEmpty());
        }
        $pathGroup->setPaths($newPaths);
    }
}
