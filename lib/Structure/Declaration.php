<?php

namespace MySheet\Structure;

use MySheet\Structure\RuleValue;
use MySheet\Traits\RootClassTrait;
use MySheet\Traits\HandlerCallTrait;
use MySheet\Error\ParseException;
use MySheet\Error\ErrorTable;
use MySheet\Essentials\VariableScope;

require_once 'RuleGroup' . EXT;

/**
 * Description of Selector
 *
 * @author dobby007
 */
class Declaration {
    use RootClassTrait, HandlerCallTrait;
    
    /**
     * @var string
     */
    private $ruleName;

    /**
     * @var RuleValue
     */
    private $ruleValue;
    
    
    public function __construct($root, $declaration) {
        $this->setRoot($root);
        $this->setDeclaration($declaration);
    }
    
    public function getRuleName() {
        return $this->ruleName;
    }
    
    public function setRuleName($ruleName) {
        $this->ruleName = strtolower(trim($ruleName));
    }
    
    /**
     * 
     * @return RuleValue
     */
    public function getRuleValue() {
        return $this->ruleValue;
    }

    public function setRuleValue($ruleValue) {
        if (is_string($ruleValue))
            $ruleValue = new RuleValue($this->getRoot(), $ruleValue);
        
        if ($ruleValue instanceof RuleValue) {
            $this->ruleValue = $ruleValue;
        }
    }
    
    public function setDeclaration($declaration) {
        $right_declaration = self::canBeDeclaration($declaration, $matches);
        
        if ($right_declaration) {
            $this->setRuleName($matches[1]);
            $this->setRuleValue($matches[2]);
        } else {
            throw new ParseException(ErrorTable::E_BAD_SELECTOR, [$declaration]);
        }
    }
    
    public function toRealCss(VariableScope $arguments = null) {
        $result = $this->renderCssEvent($this, $arguments);
        if ($result->handled()) {
            return $result->result();
        }
        
        return $this->getRuleName() . ': ' . $this->getRuleValue()->getValue($arguments);
    }
    
    public function __toString() {
        return (string)$this->toRealCss();
    }
    
    public static function canBeDeclaration($string, &$matches = null) {
        $res = !!preg_match('/^([-a-z][a-z\d_-]*)\s*(?::|\s)\s*([\d"\'.a-z$@].*)$/i', $string, $matches);
//        var_dump('REGEX::: ', $string,$res, $matches);
        return $res;
    }
}
