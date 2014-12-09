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
    protected $className;
    protected $typeName;
    protected $namespace;
    
    public function __construct($className, $typeName, $namespace) {
        $this->setClassName($className);
        $this->setNamespace($namespace);
        $this->setTypeName($typeName);
    }
    
    public function getClassName() {
        return ucfirst($this->className);
    }

    public function getTypeName() {
        return $this->typeName;
    }

    public function getNamespace() {
        return $this->namespace;
    }
    
    protected function setClassName($className) {
        $this->className = ucfirst($className);
        return $this;
    }

    protected function setTypeName($typeName) {
        $this->typeName = $typeName;
        return $this;
    }

    protected function setNamespace($namespace) {
        $this->namespace = $namespace;
        return $this;
    }

    public function getFullClass() {
        return $this->getNamespace() . '\\' . $this->getClassName() . $this->getTypeName();
    }
    
    public function getShortName() {
        return lcfirst($this->getClassName());
    }
    
    public function classExists($autoload = true) {
        return class_exists($this->getFullClass(), $autoload);
    }
    
}
