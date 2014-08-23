<?php

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
    use RootClassTrait, HandlerCallTrait;
    
    /**
     * @var string
     */
    private $ruleName;

    /**
     * @var RuleValue
     */
    private $ruleValue;
    
    
    public function __construct($declaration) {
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
            $ruleValue = new RuleValue($ruleValue);
        
        if ($ruleValue instanceof RuleValue) {
            $this->ruleValue = $ruleValue;
        }
    }
    
    public function setDeclaration($declaration) {
        $right_declaration = is_string($declaration) && self::canBeDeclaration(trim($declaration), $matches);
        
        if ($right_declaration) {
            $this->setRuleName($matches[1]);
            $this->setRuleValue($matches[2]);
        } else {
            throw new ParseException(null, 'BAD_DECLARATION', [$declaration]);
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
        $res = !!preg_match('/^([-a-z][a-z\d_-]*)\s*(?::|\s)\s*([-#\d"\'.a-z$@].*)$/i', $string, $matches);
//        var_dump('REGEX::: ', $string,$res, $matches);
        return $res;
    }
}
