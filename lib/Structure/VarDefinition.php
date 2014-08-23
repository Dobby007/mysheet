<?php

namespace MSSLib\Structure;

use MSSLib\Structure\Selector;
use MSSLib\Structure\Declaration;
use MSSLib\Structure\RuleGroup;
use MSSLib\Helpers\ArrayHelper;
use MSSLib\Essentials\StringBuilder;
use MSSLib\Essentials\VariableScope;

/**
 * Description of Ruleset
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class VarDefinition extends LeafBlock {
    protected $varName = null;
    protected $varExpression = null;
    
    public function __construct($parent, $name, $expression) {
        parent::__construct($parent);
        $this->setVarName($name);
        $this->setVarExpression($expression);
    }
    
    public function getVarName() {
        return $this->varName;
    }

    public function getVarExpression() {
        return $this->varExpression;
    }

    public function setVarName($varName) {
        $this->varName = $varName;
        return $this;
    }

    public function setVarExpression($varExpression) {
        $this->varExpression = $varExpression;
        return $this;
    }
        
    protected function compileRealCss(VariableScope $vars = null) {
        var_dump('DEFINE VAR: ' . $this->getVarName());
        $this->getRoot()->getVars()->set($this->getVarName(), 'aa');
    }
}
