<?php

namespace MySheet\Structure;

use MySheet\Structure\Selector;
use MySheet\Structure\Declaration;
use MySheet\Structure\RuleGroup;
use MySheet\Helpers\ArrayHelper;
use MySheet\Essentials\StringBuilder;

/**
 * Description of Ruleset
 *
 * @author dobby007
 */
class MediaRequest extends NodeBlock {
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

        
    protected function compileRealCss(VariableScope $vars = null) {
        $request = $this->getRequest();
        $childrenLines = parent::compileRealCss();
        if ($request) {
            var_dump('MEDIA REQUEST COMPILATION: '. $request);
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
