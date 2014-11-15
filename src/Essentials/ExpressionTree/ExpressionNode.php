<?php

/*
 * Copyright 2014 dobby007 (Alexander Gilevich, alegil91@gmail.com).
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

namespace MSSLib\Essentials\ExpressionTree;

use Tree\Node\Node;
use MSSLib\Helpers\ExpressionTreeHelper;
use MSSLib\Essentials\VariableScope;

/**
 * Description of OperatorNode
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class ExpressionNode extends Node implements ICalculatedNode
{
    
    public function __construct(array $children = []) {
        $this->setExpression($children);
    }
    
    public function setExpression($value) {
        if (is_array($value)) {
            $this->setChildren($value);
        } else if ($value instanceof Node) {
            $this->setChildren($value->getChildren());
        }
        
        return $this;
    }
    
    /**
     * Gets internal expression as array of nodes (a.k.a. getChildren)
     * @return array
     */
    public function getExpression() {        
        return $this->getChildren();
    }
    
    public function getCalculatedValue(VariableScope $vars = null) {
        return ExpressionTreeHelper::calculateExpression($this, $vars);
    }
}