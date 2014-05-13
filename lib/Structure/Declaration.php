<?php

namespace MySheet\Structure;

use MySheet\Structure\RuleValue;

/**
 * Description of Selector
 *
 * @author dobby007
 */
class Declaration {
    use \MySheet\Traits\RootClassTrait;
    
    private $ruleName;
    private $ruleValue = array();
    
    
    public function __construct($declaration) {
        $this->setDeclaration($declaration);
//        $this->setRoot($root);
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
        $this->ruleValue = new RuleValue($ruleValue);
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
        $result = $this->getRoot()->getHandlerFactory()->executeHandler('Declaration', 'renderCss', null, $handled);
        if ($handled) {
            return $result;
        }
        
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
