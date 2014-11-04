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

namespace MSSLib\Functionals\RuleParam;

use MSSLib\Essentials\RuleParam;
use MSSLib\Helpers\StringHelper;
use MSSLib\Essentials\ExpressionTree\ExpressionNode;
use MSSLib\Essentials\ExpressionTree\OperatorNode;
use MSSLib\Essentials\ExpressionTree\ParamNode;

/**
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class MathExprParam extends RuleParam {
    protected $text;
    
    public function __construct($expression) {
        if (is_string($expression)) {
            self::parseIntoTree($expression);
        } else {
            $this->setText($expression);
        }
    }

    public function getText() {
        return $this->text;
    }

    public function setText($text) {
        $this->text = $text;
    }

    public function toRealCss() {
        var_dump($this->text);
        return 'queue';
    }
    
    public function __toString() {
        return $this->toRealCss();
    }
    
    protected static function parseIntoTree(&$expression) {
        $rootTreeNode = $treeNode = new ExpressionNode();
        $expression = ltrim($expression);
        while (strlen($expression) > 0) {
            $nodeChildren = $treeNode->getChildren();
            /** @todo Parse only strings with preceeding open bracket */
            $subExpression = StringHelper::parseEnclosedString($expression);
            if (strlen($subExpression) > 0) {
                $subExpression = substr($subExpression, 1, -1);
                $subExpressionNode = self::parseIntoTree($subExpression);
                //if returned type is Node and $subExpression is empty string after the call
                if ($subExpressionNode instanceof ExpressionNode && empty($subExpression)) {
                    $treeNode->addChild((new ExpressionNode())->setValue($subExpressionNode));
                } else {
                    return false;
                }
            } else if (in_array($expression[0], ['+', '-'])) {
                if (count($nodeChildren) === 0 || end($nodeChildren) instanceof OperatorNode) {
                    return false;
                }
                $treeNode->addChild(new OperatorNode($expression[0]));
                $expression = substr($expression, 1);
            } else {
                $param = (new self(null))->parseNestedParam($expression);
                if (!$param) {
                    if (count($nodeChildren) > 1 && !(end($nodeChildren) instanceof OperatorNode)) {
                        return $rootTreeNode;
                    }
                    return false;
                }
                $treeNode->addChild(new ParamNode($param));
            }
            $expression = ltrim($expression);
        }
        
        return $rootTreeNode;
    }
       
    public static function parse(&$string) {
        if (preg_match('/[+-]/', $string, $matches)) {
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
