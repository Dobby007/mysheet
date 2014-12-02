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

namespace MSSLib\EmbeddedClasses;

use MSSLib\Essentials\MssClass;
use MSSLib\Helpers\StringHelper;
use MSSLib\Essentials\ExpressionTree\ExpressionNode;
use MSSLib\Essentials\ExpressionTree\OperatorNode;
use MSSLib\Essentials\ExpressionTree\ParamNode;
use MSSLib\Helpers\ExpressionTreeHelper;
use MSSLib\Essentials\VariableScope;
use MSSLib\Helpers\MssClassHelper;

/**
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class MathExprClass extends MssClass {
    protected $expressionTree;
    protected static $registeredOperators = null;
    
    public function __construct($expression) {
        if (is_string($expression)) {
            self::parseIntoTree($expression);
        } else {
            $this->setExpressionTree($expression);
        }
    }

    public function getValue(VariableScope $vars) {
        $node = $this->getExpressionTree();
        if ($node) {
            return $node->getCalculatedValue($vars);
        }
        return null;
    }
    
    /**
     * 
     * @return ExpressionNode
     */
    public function getExpressionTree() {
        return $this->expressionTree;
    }

    /**
     * 
     * @param ExpressionNode $expressionTree
     * @return $this
     */
    public function setExpressionTree($expressionTree) {
        $this->expressionTree = $expressionTree;
        return $this;
    }
    
    public function toRealCss(VariableScope $vars) {
        $calculatedValue = $this->getValue($vars);
        if ($calculatedValue) {
            return $calculatedValue->toRealCss($vars);
        }
        return null;
    }
    
    public function __toString() {
        return $this->toRealCss();
    }
    
    protected static function parseIntoTree(&$expression, $needNormalization = true) {
        $rootTreeNode = $treeNode = new ExpressionNode();
        $expression = ltrim($expression);
        while (strlen($expression) > 0) {
            $nodeChildren = $treeNode->getChildren();
            $expressionCopy = $expression;
            /** @todo Parse only strings with preceeding open bracket */
            $subExpression = StringHelper::parseEnclosedString($expression);
            if (strlen($subExpression) > 0) {
                $subExpression = substr($subExpression, 1, -1);
                $subExpressionNode = self::parseIntoTree($subExpression, false);
                //if returned type is Node and $subExpression is empty string after the call
                if ($subExpressionNode instanceof ExpressionNode && empty($subExpression)) {
                    $treeNode->addChild((new ExpressionNode())->setExpression($subExpressionNode));
                } else {
                    return false;
                }
            } else if (in_array($expressionCopy[0], self::$registeredOperators, true) && ($operator = \MSSLib\Helpers\OperatorHelper::parseOperator($expressionCopy))) {
                if (count($nodeChildren) === 0 || end($nodeChildren) instanceof OperatorNode) {
                    return false;
                }
                $treeNode->addChild(new OperatorNode($operator));
                $expression = $expressionCopy;
            } else {
                if (count($nodeChildren) > 0 && !(end($nodeChildren) instanceof OperatorNode)) {
                    return $rootTreeNode;
                }
                
                $param = MssClassHelper::parseMssClass($expressionCopy, array('mathExpr', 'sequence'), true);
                if (!$param) {
                    if (
                        count($nodeChildren) > 1 && 
                        !(end($nodeChildren) instanceof OperatorNode)
                    ) {
                        return $rootTreeNode;
                    }
                    return false;
                } else if (
                    $param instanceof MssClass && 
                    (count($nodeChildren) === 0 || end($nodeChildren) instanceof OperatorNode)
                ) {
                    $expression = $expressionCopy;
                    $treeNode->addChild(new ParamNode($param));
                } else {
                    return false;
                }
            }
            $expression = ltrim($expression);
        }
        
        return $needNormalization ? ExpressionTreeHelper::normalizeTree($rootTreeNode) : $rootTreeNode;
    }
       
    public static function parse(&$string) {
        
        if (self::$registeredOperators === null) {
            self::$registeredOperators = self::getRootObj()->getListManager()->getList('Operator')->map(function ($operatorClass) {
                return $operatorClass::operatorSymbol();
            });
        }
        
        // it's very likely that string contains mathematical expression if it contains operators
        // also one-operand expression is also valid MathExpr
        if (
                $string[0] === '(' || 
                preg_match('/[' . implode('\\', self::$registeredOperators) . ']/', $string, $matches)
        ) {
            $strCopy = $string;
            $result = self::parseIntoTree($strCopy);
            if ($result instanceof ExpressionNode && count($result->getChildren()) > 0) {
                $string = $strCopy;
            } else {
                return false;
            }
            return new self($result);
        }
        return false;
    }
}
