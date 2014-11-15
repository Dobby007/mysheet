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

use MSSLib\Structure\Selector;
use MSSLib\Structure\Declaration;
use MSSLib\Structure\RuleGroup;
use MSSLib\Helpers\ArrayHelper;
use MSSLib\Essentials\StringBuilder;
use MSSLib\Essentials\VariableScope;
use MSSLib\Tools\Debugger;

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
        
    protected function compileRealCss(VariableScope $vars) {
        Debugger::logString('DEFINE VAR: ' . $this->getVarName());
        $vars->set($this->getVarName(), $this->getVarExpression());
    }
}
