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

namespace MSSLib\Structure;

use MSSLib\Structure\RuleValue;
use MSSLib\Traits\RootClassTrait;
use MSSLib\Traits\HandlerCallTrait;
use MSSLib\Error\ParseException;
use MSSLib\Error\ErrorTable;
use MSSLib\Essentials\VariableScope;


/**
 * Description of Selector
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class Declaration {
    use RootClassTrait,
        HandlerCallTrait;
    
    /**
     * @var string
     */
    private $ruleName;

    /**
     * @var RuleValue
     */
    private $ruleValue;
    
    private $ruleEnabled = true;
    
    public function __construct($declaration) {
        $this->setDeclaration($declaration);
    }
    
    public function getRuleName() {
        return $this->ruleName;
    }
    
    public function setRuleName($ruleName) {
        $this->ruleName = strtolower(trim($ruleName));
        return $this;
    }
    
    /**
     * 
     * @return RuleValue
     */
    public function getRuleValue() {
        return $this->ruleValue;
    }

    public function setRuleValue($ruleValue) {
        if (is_string($ruleValue)) {
            $ruleValue = new RuleValue($ruleValue);
        }
        
        if ($ruleValue instanceof RuleValue) {
            $this->ruleValue = $ruleValue;
        }
        
        return $this;
    }
    
    public function getRuleEnabled() {
        return $this->ruleEnabled;
    }

    public function setRuleEnabled($ruleEnabled) {
        $this->ruleEnabled = !!$ruleEnabled;
        return $this;
    }

        
    public function setDeclaration($declaration) {
        $right_declaration = is_string($declaration) && self::canBeDeclaration(trim($declaration), $matches);
        
        if ($right_declaration) {
            if ($matches[1] === '~') {
                $this->setRuleEnabled(false);
            }
            $this->setRuleName($matches[2]);
            $this->setRuleValue($matches[3]);
        } else {
            throw new ParseException(null, 'BAD_DECLARATION', [$declaration]);
        }
    }
    
    public function toRealCss(VariableScope $vars) {
        if (!$this->getRuleEnabled()) {
            return null;
        }
        
        $result = $this->renderCssEvent($this, $vars);
        if ($result->handled()) {
            return $result->result();
        }
        
        return $this->getRuleName() . ': ' . $this->getRuleValue()->getValue($vars);
    }
    
    public static function canBeDeclaration($string, &$matches = null) {
        $res = !!preg_match('/^(~)?([-a-z][a-z\d_-]*)\s*(?::|\s)\s*([-#\d"\'.a-z($@].*)$/i', $string, $matches);
//        var_dump('REGEX::: ', $string,$res, $matches);
        return $res;
    }
}
