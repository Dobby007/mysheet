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

/**
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class MathExprClass extends MssClass {
    protected $expressionTree;
    
    public function __construct($expression) {
        if (is_string($expression)) {
            self::parseIntoTree($expression);
        } else {
            $this->setExpressionTree($expression);
        }
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
    
    public function toRealCss() {
        $node = $this->getExpressionTree();
        if ($node) {
            return $node->getValue();
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
            } else if ($operator = \MSSLib\Helpers\OperatorHelper::parseOperator($expression)) {
                if (count($nodeChildren) === 0 || end($nodeChildren) instanceof OperatorNode) {
                    return false;
                }
                $treeNode->addChild(new OperatorNode($operator));
                $expression = substr($expression, 1);
            } else {
                $param = (new self(null))->parseNestedParam($expression);
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
        static $registeredOperators = null;
        if ($registeredOperators === null) {
            $registeredOperators = self::getRootObj()->getListManager()->getList('Operator')->map(function ($operatorClass) {
                return $operatorClass::operatorSymbol();
            });
        }
        
        //it's very likely that string contains mathematical expression if it contains operators
        if (preg_match('/[' . implode('\\', $registeredOperators) . ']/', $string, $matches)) {
            $strCopy = $string;
            $result = self::parseIntoTree($strCopy);
            if (is_object($result)) {
                $string = $strCopy;
            } else {
                return false;
            }
            return new self($result);
        }
        return false;
    }
}
