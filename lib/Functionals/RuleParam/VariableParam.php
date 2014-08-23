<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MSSLib\Functionals\RuleParam;

use MSSLib\Essentials\RuleParam;
use MSSLib\Essentials\VariableScope;
use MSSLib\Error\ParseException;

/**
 * Class that represents variable in rule value (RuleValue). It is rule parameter (RuleParam).
 * It is used to reference some expression outside current rule set.
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class VariableParam extends RuleParam {
    private $varName = '';
    
    public function __construct($varName) {
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
            throw new ParseException(null, 'BAD_VARIABLE_NAME');
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
