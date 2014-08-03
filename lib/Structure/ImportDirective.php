<?php

namespace MySheet\Structure;

use MySheet\Structure\RuleValue;
use MySheet\Functionals\RuleParam\StringParam;
use MySheet\Essentials\VariableScope;

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
                $url instanceof StringParam && 
                substr($url->getText(), -4) === '.mss'
            ) {
                var_dump('IMPORT COMPILATION: '. $url);
                $url = str_replace('..', '', $url->getText());
                $resultDoc = $this->getRoot()->parseImportFile($url);
                if ($resultDoc instanceof Block) {
//                    var_dump('Result Document:', $resultDoc);
                    return $resultDoc->toRealCss(false, false);
                }
                
            } else {
                return '@import ' . $value;
            }
        }
        
    }
}
