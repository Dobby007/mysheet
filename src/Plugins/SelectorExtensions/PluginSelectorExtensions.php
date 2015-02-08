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

namespace MSSLib\Plugins\SelectorExtensions;

use MSSLib\Plugins\PluginBase;
use MSSLib\Essentials\VariableScope;
use MSSLib\Helpers\StringHelper;
use MSSLib\Structure\PathGroup;
use MSSLib\Structure\Selector;


/**
 * Description of Mixin
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class PluginSelectorExtensions extends PluginBase {
    private $handlers = array('parent', 'any');
    
    
    public function init() {
        self::getRootObj()->getHandlerFactory()->registerHandler('Selector', 'cssSelectorParsing', function(&$handled, Selector $selector, PathGroup $pathGroup) {
            foreach ($this->handlers as $handler) {
                $method_name = 'parse' . ucfirst($handler) . 'Handler';
                $this->$method_name($selector, $pathGroup);
            }
        });
    }
    
    public function parseParentHandler(Selector $selector, PathGroup $pathGroup) {
        $newPaths = [];
        $parentPaths = $selector->getRuleset()->getParentPaths();
        
        if (empty($parentPaths)) {
            $parentPaths = [''];
        }
        
        foreach ($pathGroup->getPaths() as $path) {
            foreach ($parentPaths as $parentPath) {
                $newPath = str_replace('&', (string)$parentPath, $path, $count);
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
                $function = StringHelper::parseFunction($startPart, true, false);
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
            $leftAnyCount = substr_count($path, ':any');
            // if there are no ':any' substrings we just skip this iteration
            if ($leftAnyCount < 1) {
//                $newPaths[] = $path;
//                continue;
            }
            
            $queue->enqueue($path);
            do {
                $path = $queue->dequeue();
                $subSelectors = $findAnyMatches($path, $pos, $pattern);
                $replaces = [];
                if ($subSelectors === false) {
                    $newPaths[] = $path;
                } else {
                    $replaces = StringHelper::replaceSubstring($pattern, $subSelectors, $path);
                    // we can exit the cycle once we are sure that there are no more ':any' substrings
                    if (--$leftAnyCount === 0) {
                        \MSSLib\Helpers\ArrayHelper::concat($newPaths, $replaces);
                        break;
                    }
                    
                    foreach ($replaces as $replace) {
                        $queue->enqueue($replace);
                    }
                }
            } while (!$queue->isEmpty());
        }
        $pathGroup->setPaths($newPaths);
    }
}
