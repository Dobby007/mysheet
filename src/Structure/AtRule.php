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

use MSSLib\Essentials\StringBuilder;
use MSSLib\Essentials\VariableScope;
use MSSLib\Tools\Debugger;
use MSSLib\Helpers\ArrayHelper;

/**
 * Description of Ruleset
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class AtRule extends NodeBlock {
    protected $_name;
    protected $_parameters;
    
    public function getName() {
        return $this->_name;
    }

    public function getParameters() {
        return $this->_parameters;
    }

    public function setName($name) {
        $this->_name = $name;
        return $this;
    }

    public function setParameters($parameters) {
        $this->_parameters = $parameters;
        return $this;
    }
        
    protected function compileRealCss(VariableScope $vars, StringBuilder $output) {
        $innerContent = null;
        if ($this->hasChildren()) {
            $innerContent = new StringBuilder();
            $this->compileChildren($vars, $innerContent);
        }
        
        Debugger::logString('COMPILATION OF AT-RULE: '. $this->getName());
        $output->addLine('@' . $this->getName() . ' ' . $this->getParameters() . ($innerContent === null ? ';' : ''));
        $rules = $this->renderChildrensCssRuleGroup($vars);
        if (!empty($rules) || $innerContent !== null) {
            $output->appendText(
                $this->getSetting('cssRenderer.prefixOCB', ' ') .
                '{' .
                $this->getSetting('cssRenderer.suffixOCB', "\n")
            );
            if (!empty($rules)) {
                $compiled_rules = ArrayHelper::implodeLines(
                    $rules, $this->getSetting('cssRenderer.prefixRule', '    '), $this->getSetting('cssRenderer.suffixRule', ''), $this->getSetting('cssRenderer.sepRules', ";\n")
                );
                $output->appendText(
                    $this->getSetting('cssRenderer.prefixDeclBlock', "") .
                    $compiled_rules .
                    $this->getSetting('cssRenderer.suffixDeclBlock', "")
                );
            } else {
                $output->appendText(
                    $innerContent->processLines(
                        $this->getSetting('cssRenderer.prefixAtRuleLine', '    '), 
                        $this->getSetting('cssRenderer.suffixAtRuleLine', '')
                    )
                );
            }
            
            $output->appendText(
                $this->getSetting('cssRenderer.prefixCCB', "\n") .
                '}' .
                $this->getSetting('cssRenderer.suffixCCB', "\n")
            );
        }
        
    }
}
