<?php

/*
 * Copyright 2015 dobby007.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace MSSLib\Essentials;

use MSSLib\Structure\Ruleset;
use MSSLib\Structure\NodeBlock;

/**
 * Description of RulesetFinder
 *
 * @author dobby007
 */
class RulesetFinder 
{
    const FIND_ONLY_CSS_SELECTORS = 0b1;
    const FIND_ONLY_MSS_SELECTORS = 0b10;
    const FIND_ANY_SELECTOR = 0b11;
    
    const DEFAULT_FIND_FLAGS = 0b11;
    
    /**
     * @return First matched element or null otherwise
     */
    public static function querySelector($selector, NodeBlock $rootBlock, $flags = self::DEFAULT_FIND_FLAGS) {
        return self::querySelectorsInternal($selector, $rootBlock, true, $flags);
    }

    /**
     * @return  Array of matched elements
     */
    public static function querySelectorAll($selector, NodeBlock $rootBlock, $flags = self::DEFAULT_FIND_FLAGS) {
        return self::querySelectorsInternal($selector, $rootBlock, false, $flags);
    }
    
    private static function querySelectorsInternal($selector, NodeBlock $rootBlock, $stopOnFirst, $flags) {
        $block = $rootBlock;
        $path = new \SplStack();
        $index = 0;
        $result = $stopOnFirst ? null : [];
        while ($block) {
            $matches = [];
            if ($block instanceof Ruleset) {
                $matches = $block->getMatchedSelectors($selector, $flags);
                foreach ($matches as $match) {
                    if (empty($match)) {
                        if ($stopOnFirst) {
                            return $block;
                        }
                        $result[] = $block;
                        break;
                    }
                }
            }
            $parentChildren = !$path->isEmpty() ? $path->top()->getBlock()->getChildren() : null;
            if ($block->hasChildren()) {
                $path[] = new PathBreadcrumb($block, $index, $matches);
                $index = 0;
                $block = $block->getChild($index);
            } else if (isset($parentChildren[$index + 1])) {
                $index++;
                $block = $parentChildren[$index];
                $path->top()->setIndex($index);
            } else if (!$path->isEmpty()) {
                do {
                    $path->pop();
                    if ($path->isEmpty()) {
                        return $result;
                    }
                } while (!$path->top()->blockHasChild($path->top()->getIndex() + 1));
                $index = $path->top()->getIndex() + 1;
                $path->top()->setIndex($index);
                $block = $path->top()->getBlock()->getChild($index);
            } else {
                break;
            }
        }
        return $result;
    }

}

class PathBreadcrumb 
{

    private $_block;
    private $_index;
    private $_matched;

    public function __construct($block, $index, $matched) {
        $this->_block = $block;
        $this->_index = $index;
        $this->_matched = $matched;
    }

    public function blockHasChild($index) {
        $children = $this->_block->getChildren();
        return isset($children[$index]);
    }

    public function getBlock() {
        return $this->_block;
    }

    public function getIndex() {
        return $this->_index;
    }

    public function getMatched() {
        return $this->_matched;
    }
    
    public function setIndex($index) {
        $this->_index = $index;
        return $this;
    }

    public function incrementIndex($delta) {
        $this->_index = $this->_index + $delta;
        return $this;
    }

}
