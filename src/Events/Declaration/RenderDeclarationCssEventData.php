<?php

/*
 * Copyright 2015 dobby007.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace MSSLib\Events\Declaration;

use MSSLib\Events\EventData;
use MSSLib\Structure\CssRuleGroup;
use MSSLib\Structure\Declaration;

/**
 * Description of RenderCssEventData
 *
 * @author dobby007
 */
class RenderDeclarationCssEventData extends EventData
{
    protected $_declaration;
    protected $_ruleGroup;
    protected $_ruleNeglection = false;
    
    public function __construct($declaration, $ruleGroup, $vars) {
        $this->_declaration = $declaration;
        $this->_ruleGroup = $ruleGroup;
        $this->_vars = $vars;
    }
    
    /**
     * 
     * @return Declaration
     */
    public function getDeclaration() {
        return $this->_declaration;
    }

    /**
     * 
     * @return CssRuleGroup
     */
    public function getRuleGroup() {
        return $this->_ruleGroup;
    }

    public function getRuleNeglection() {
        return $this->_ruleNeglection;
    }

    public function setRuleNeglection($ruleNeglection) {
        $this->_ruleNeglection = $ruleNeglection;
    }


    
}
