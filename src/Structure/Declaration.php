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
use MSSLib\Traits\FireEventTrait;
use MSSLib\Error\ParseException;
use MSSLib\Essentials\VariableScope;
use MSSLib\Etc\Constants;

/**
 * Description of Selector
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class Declaration
{
    use RootClassTrait,
        FireEventTrait;
    
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
        if (!empty($declaration)) {
            $this->setDeclaration($declaration);
        }
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
            $ruleValue = new RuleValue($ruleValue, $this);
        }
        
        if ($ruleValue instanceof RuleValue) {
            $this->ruleValue = $ruleValue;
            $ruleValue->setParentDeclaration($this);
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
        $right_declaration = is_string($declaration) && !empty($declaration) && self::canBeDeclaration(trim($declaration), $matches);
        
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
        $ruleGroup = new CssRuleGroup();
        $eventData = new \MSSLib\Events\Declaration\RenderDeclarationCssEventData($this, $ruleGroup, $vars);
        $this->renderCssEvent($eventData);
        if (!$eventData->getRuleNeglection()) {
            $ruleGroup->addRule($this->getRuleName(), $this->getRuleValue()->toRealCss($vars));
        }
        unset($eventData);
        return $ruleGroup;
    }
    
    public static function canBeDeclaration($string, &$matches = null) {
        $res = !!preg_match('/^(~)?([-a-z][a-z\d_-]*)\s*(?::|\s)\s*([-#\d"\'.a-z($@].*)$/i', $string, $matches);
//        var_dump('REGEX::: ', $string,$res, $matches);
        return $res;
    }
    
    
    public static function getBrowserPrefixesAsArray($browsersEnum) {
        static $prefixMap = [
            Constants::BROWSER_IE => '-ms-',
            Constants::BROWSER_MOZILLA => '-moz-',
            Constants::BROWSER_OPERA => '-o-',
            Constants::BROWSER_WEBKIT => '-webkit-'
        ];
        $result = [];
        foreach ($prefixMap as $browserId => $prefix) {
            if ($browsersEnum & $browserId) {
                $result[] = $prefix;
            }
        }
        return $result;
    }
    
    /**
     * Creates and returns array of declarations based on the provided browser prefixes
     * @param string $ruleName
     * @param mixed $ruleValue
     * @param string[] $prefixes
     * @return Declaration[]
     */
    public static function createPrefixedDeclarations($ruleName, $ruleValue, $browsersEnum = Constants::BROWSER_ALL, $addNonPrefixedVersion = true) {
        $result = [];
        $prefixes = self::getBrowserPrefixesAsArray($browsersEnum);
        if ($addNonPrefixedVersion) {
            $prefixes[] = '';
        }
        
        foreach ($prefixes as $prefix) {
            $name = $prefix . $ruleName;
            if ($ruleValue instanceof RuleValue) {
                $result[] = (new Declaration(null))->setRuleName($name)->setRuleValue($ruleValue);
            }
        }
        return $result;
    }
}
