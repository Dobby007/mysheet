<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Functionals\RuleParam;

use MySheet\Essentials\RuleParam;
use MySheet\Essentials\VariableScope;

/**
 * Description of VariableParam
 *
 * @author dobby007
 */
class VariableParam extends RuleParam {
    private $varName = '';
    
    function __construct($varName) {
        $this->setVarName($varName);
        $this->varName = $varName;
    }

    
    public function getVarName() {
        return $this->varName;
    }

    public function setVarName($varName) {
        if (VariableScope::canBeVariable($varName)) {
            $this->varName = $varName;
        } else {
            //throw
        }
    }

        
    public function toRealCss(VariableScope $vars = null) {
        $vars = $this->getRoot()->getVars()->createScope($vars);
        $varval = $vars[$this->getVarName()];
        
        return is_array($varval) ? implode(' ', $varval) : (string) $varval;
    }

    
    public static function parse(&$string) {
        if (preg_match('/^\$([\S]+)/i', $string, $matches)) {
            parent::trimStringBy($string, strlen($matches[0]));
            return new self($matches[1]);
        }
        return false;
    }

}
