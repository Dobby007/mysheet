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
use MSSLib\Structure\CssRuleGroup;
use MSSLib\Helpers\ArrayHelper;
use MSSLib\Essentials\StringBuilder;
use MSSLib\Essentials\VariableScope;
use MSSLib\Tools\Debugger;
use MSSLib\Essentials\BlockInterfaces\IMayContainRuleset;

/**
 * Description of Ruleset
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class MediaRequest extends NodeBlock implements IMayContainRuleset {
    protected $request = null;
    
    public function __construct($parent) {
        parent::__construct($parent);
    }
    
    public function getRequest() {
        return $this->request;
    }

    public function setRequest($request) {
        if (is_string($request)) {
            $request = preg_replace('/\s+/', ' ', $request);
            $this->request = empty($request) ? null : $request;
        }
    }

        
    protected function compileRealCss(VariableScope $vars) {
        $request = $this->getRequest();
        $childrenLines = parent::compileRealCss($vars);
        if ($request) {
            Debugger::logString('MEDIA REQUEST COMPILATION: '. $request);
            $lines = new StringBuilder();
            $lines->addLine('@media ' . $request);
            $lines->appendText(
                    $this->getSetting('cssRenderer.prefixOCB', ' ') .
                    '{' .
                    $this->getSetting('cssRenderer.suffixOCB', "\n")
                )
                ->appendText(
                    $childrenLines->processLines(
                        $this->getSetting('cssRenderer.prefixMediaLine', '    '), 
                        $this->getSetting('cssRenderer.suffixMediaLine', '')
                    )
                )
                ->appendText(
                    $this->getSetting('cssRenderer.prefixCCB', "\n") .
                    '}' .
                    $this->getSetting('cssRenderer.suffixCCB', "\n")
            );
            return $lines;
        } else {
            return $childrenLines;
        }
        
    }
}
