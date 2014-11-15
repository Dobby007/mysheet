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

namespace MSSLib\Helpers;

use MSSLib\Essentials\ExpressionTree\ExpressionNode;
use MSSLib\Essentials\ExpressionTree\OperatorNode;
use MSSLib\Essentials\ExpressionTree\ParamNode;
use MSSLib\Essentials\VariableScope;

/**
 * Description of ExpressionTreeHelper
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class ExpressionTreeHelper
{
    public static function normalizeTree(ExpressionNode $node) {
        $children = $node->getChildren();
        $needRestructuring = count($children) > 3;
        $operationsPrecedence = new \SplPriorityQueue();
        
        foreach ($children as $key => $child) {
            if ($needRestructuring && $child instanceof OperatorNode) {
                $operationsPrecedence->insert([$key, $child], $child->getOperatorPriority());
            } else if ($child instanceof ExpressionNode) {
                self::normalizeTree($child);
            }
        }
        
        while (!$operationsPrecedence->isEmpty() && count($children) > 3) {
            $operation = $operationsPrecedence->extract();
            $index = $operation[0];
            $operator = $operation[1];
            if ($index > 0) {
                $extractedChildrenPart = array_slice($children, $index - 1, 3);
                array_splice($children, $index - 1, 3, [(new ExpressionNode($extractedChildrenPart))]);
            }
        }
        
        if ($needRestructuring) {
            $node->setChildren($children);
        }
        
        return $node;
    }
    
    public static function calculateExpression(ExpressionNode $node, VariableScope $vars = null) {
        /* @var $children \Tree\Node\NodeInterface[] */
        $children = $node->getChildren();
        $countChildren = count($children);
        if ($countChildren === 3) {
            //binary operator
            if (!($children[1] instanceof OperatorNode)) {
                //throw
            }
            return $children[1]->getOperator()->calculate($children[0]->getCalculatedValue($vars), $children[2]->getCalculatedValue($vars));
        } else if ($countChildren === 1) {
            //expression contains only one Operand
            return $children[0]->getCalculatedValue($vars);
        } else if ($countChildren === 2) {
            //unary operator
            if (!($children[0] instanceof OperatorNode)) {
                //throw
            }
            return $children[0]->getOperator()->calculate($children[1]->getCalculatedValue($vars));
        } else if ($countChildren === 0) {
            return null;
        } else {
            //not normalized expression
            //throw
            return;
        }
        
        
        foreach ($children as $key => $child) {
            if ($needRestructuring && $child instanceof OperatorNode) {
                $operationsPrecedence->insert([$key, $child], $child->getOperatorPriority());
            } else if ($child instanceof ExpressionNode) {
                self::normalizeTree($child);
            }
        }
    }
}
