<?php

namespace MySheet\Structure;

/**
 * Description of Selector
 *
 * @author dobby007
 */
class Declaration {
    private $ruleName;
    private $ruleValue;
    
    public function __construct($declaration) {
        $this->setDeclaration($declaration);
    }
    
    public function getRuleName() {
        return $this->ruleName;
    }
    
    public function setRuleName($ruleName) {
        $this->ruleName = trim($ruleName);
    }
    
    public function getRuleValue() {
        return $this->ruleValue;
    }

    public function setRuleValue($ruleValue) {
        $this->ruleValue = trim($ruleValue);
    }
    
    public function setDeclaration($declaration) {
        $right_declaration = self::canBeDeclaration($declaration, $matches);
        
        if ($right_declaration) {
            $this->setRuleName($matches[1]);
            $this->setRuleValue($matches[2]);
        } else {
            throw new ParseException(ErrorTable::E_BAD_SELECTOR);
        }
    }
    
    public function toRealCss() {
        return $this->getRuleName() . ': ' . $this->getRuleValue();
    }
    
    public function __toString() {
        return $this->toRealCss();
    }
    
    public static function canBeDeclaration($string, &$matches = null) {
        $res = !!preg_match('/^([a-z][a-z\d_-]*)\s*(?::|\s)\s*([\d"\'.a-z].*)$/i', $string, $matches);
//        var_dump('REGEX::: ', $string,$res, $matches);
        return $res;
    }
}
