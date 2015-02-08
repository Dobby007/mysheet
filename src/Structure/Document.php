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

use MSSLib\Essentials\BlockInterfaces\IMayContainRuleset;

/**
 * Description of Document
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class Document extends NodeBlock implements IMayContainRuleset {
    protected $_docFilePath;
    
    public function __construct() {
        parent::__construct(null);
    }
    
    public function hasFilePath() {
        return !empty($this->_docFilePath);
    }
    
    public function getDocFilePath() {
        return $this->_docFilePath;
    }

    public function setDocFilePath($docFilePath) {
        $this->_docFilePath = $docFilePath;
        return $this;
    }
    
    public static function makeRelativeFilePath($doc, $relativeFilePath) {
        $basedir = null;
        if ($doc instanceof Document && $doc->hasFilePath()) {
            $basedir = dirname($doc->getDocFilePath());
        }
        if (!$basedir) {
            $basedir = self::getRootObj()->getActiveDirectory();
        }
            
        return $basedir ? $basedir . DIRECTORY_SEPARATOR . $relativeFilePath : $relativeFilePath;
    }
    
    protected function compileRealCss(\MSSLib\Essentials\VariableScope $vars) {
        self::getRootObj()->setActiveDocument($this);
        return parent::compileRealCss($vars);
    }

    

}
