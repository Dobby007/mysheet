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

        
    protected function compileRealCss(VariableScope $vars, StringBuilder $output) {
        $request = $this->getRequest();
        $innerContent = new StringBuilder();
        $this->compileChildren($vars, $innerContent);
        if ($request) {
            Debugger::logString('MEDIA REQUEST COMPILATION: '. $request);
            $output->addLine('@media ' . $request);
            $output->appendText(
                $this->getSetting('cssRenderer.prefixOCB', ' ') .
                '{' .
                $this->getSetting('cssRenderer.suffixOCB', "\n")
            )
            ->appendText(
                $innerContent->processLines(
                    $this->getSetting('cssRenderer.prefixAtRuleLine', '    '), 
                    $this->getSetting('cssRenderer.suffixAtRuleLine', '')
                )
            )
            ->appendText(
                $this->getSetting('cssRenderer.prefixCCB', "\n") .
                '}' .
                $this->getSetting('cssRenderer.suffixCCB', "\n")
            );
        } else {
            $output->addLines($innerContent);
        }
        
    }
}
