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

namespace MSSLib\EmbeddedClasses;

use MSSLib\Essentials\MssClass;
use MSSLib\Essentials\VariableScope;
use MSSLib\Error\ParseException;

/**
 * Class that represents variable in rule value (RuleValue). It is rule parameter (MssClass).
 * It is used to reference some expression outside current rule set.
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class VariableClass extends MssClass {
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
            throw new ParseException(null, 'BAD_VARIABLE_NAME', [$varName]);
        }
    }

    public function getValue(VariableScope $vars) {
        $varValue = $vars[$this->getVarName()];
        if ($varValue === null) {
            throw new \MSSLib\Error\CompileException(null, 'UNKNOWN_VAR', [$this->getVarName()]);
        }
        if (!($varValue instanceof MssClass)) {
            throw new \MSSLib\Error\CompileException(null, 'INCORRECT_VAR_VALUE', [$this->getVarName()]);
        }
        return $varValue->getValue($vars);
    }
        
    public function toRealCss(VariableScope $vars) {
        $varValue = $vars[$this->getVarName()];
        if ($varValue === null) {
            throw new \MSSLib\Error\CompileException(null, 'UNKNOWN_VAR', [$this->getVarName()]);
        }
        if ($varValue instanceof MssClass) {
            $varValue = $varValue->toRealCss($vars);
        }
        return is_array($varValue) ? implode(' ', $varValue) : (string)$varValue;
    }

    
    public static function parse(&$string) {
        if (preg_match('/^\$([a-z0-9_]+)/i', $string, $matches)) {
            parent::trimStringBy($string, strlen($matches[0]));
            return new self($matches[1]);
        }
        return false;
    }

}
