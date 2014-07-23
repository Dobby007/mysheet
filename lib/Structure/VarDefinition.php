<?php

namespace MySheet\Structure;

use MySheet\Structure\Selector;
use MySheet\Structure\Declaration;
use MySheet\Structure\RuleGroup;
use MySheet\Helpers\ArrayHelper;
use MySheet\Essentials\StringBuilder;
use MySheet\Essentials\VariableScope;

/**
 * Description of Ruleset
 *
 * @author dobby007
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
