<?php

/*
 * Copyright 2014 dobby007.
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

namespace MSSLib\Essentials;

/**
 * Description of TypeClassReference
 *
 * @author dobby007
 */
class TypeClassReference
{
    protected $_className;
    protected $_typeName;
    protected $_namespace;
    protected $_fullClass;
    protected $_pluginName;
    private $_updateEnabled = false;
    
    public function __construct($className, $typeName, $namespace) {
        $this->setClassName($className);
        $this->setNamespace($namespace);
        $this->setTypeName($typeName);
        $this->_updateEnabled = true;
        $this->updateFullClass();
    }
    
    protected function updateFullClass() {
        if (!$this->_updateEnabled) {
            return;
        }
        $this->_fullClass = $this->_namespace . '\\' . $this->_className . $this->_typeName;
    }
    
    public function getClassName() {
        return $this->_className;
    }
    
    public function getPluginName() {
        return $this->_pluginName;
    }

    public function getTypeName() {
        return $this->_typeName;
    }

    public function getNamespace() {
        return $this->_namespace;
    }
    
    protected function setClassName($className) {
        $parsedName = explode('\\', $className, 2);
        foreach ($parsedName as &$part) {
            $part = ucfirst($part);
        }
        if (count($parsedName) === 2) {
            $this->_pluginName = $parsedName[0];
            $this->_className = $parsedName[1];
        } else {
            $this->_className = $parsedName[0];
        }
        
        $this->updateFullClass();
        return $this;
    }

    protected function setTypeName($typeName) {
        $this->_typeName = $typeName;
        $this->updateFullClass();
        return $this;
    }

    protected function setNamespace($namespace) {
        $this->_namespace = $namespace;
        $this->updateFullClass();
        return $this;
    }

    public function getFullClass() {
        return $this->_fullClass;
    }
    
    public function getShortName() {
        return (!empty($this->_pluginName) ? lcfirst($this->_pluginName) . '\\' : '') . lcfirst($this->getClassName());
    }
    
    public function classExists($autoload = true) {
        return class_exists($this->getFullClass(), $autoload);
    }
 
    public function compareShortName($shortName) {
        return strtolower($this->getShortName()) === strtolower($shortName);
    }
}
