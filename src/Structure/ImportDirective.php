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

use MSSLib\Structure\RuleValue;
use MSSLib\EmbeddedClasses\StringClass;
use MSSLib\Essentials\VariableScope;
use MSSLib\Tools\Debugger;

/**
 * Description of Ruleset
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class ImportDirective extends LeafBlock {
    protected $importValue = null;
    
    public function __construct($parent, $sourceText) {
        parent::__construct($parent);
        $this->setSource($sourceText);
    }
    
    public function setSource($sourceText) {
        $sourceText = trim($sourceText);
        $this->importValue = new RuleValue($sourceText);
    }
    
    public function getValue() {
        return $this->importValue;
    }
        
    protected function compileRealCss(VariableScope $vars = null) {
        $value = $this->getValue();
        
        if ($value instanceof RuleValue) {
            $url = $value->getParam(0);
            if (
                $value->countParams() === 1 && 
                $url instanceof StringClass && 
                substr($url->getText(), -4) === '.mss'
            ) {
                Debugger::logString('IMPORT COMPILATION: '. $url);
                $url = str_replace('..', '', $url->getText());
                $resultDoc = $this->getRoot()->parseImportFile($url);
                if ($resultDoc instanceof Block) {
                    return $resultDoc->toRealCss(false, false);
                }
                
            } else {
                return '@import ' . $value->toRealCss() . ';';
            }
        }
        
    }
}
