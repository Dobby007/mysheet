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

namespace MSSLib\EmbeddedClasses;

use MSSLib\Essentials\MssClass;
use MSSLib\Helpers\StringHelper;
use MSSLib\Helpers\FunctionModuleHelper;
use MSSLib\Essentials\VariableScope;
use MSSLib\Helpers\MssClassHelper;
use MSSLib\Structure\Document;
use MSSLib\Tools\FileInfo;

/**
 * Internal class that allows using functions in rule values. It is a rule parameter (MssClass).
 * It represents a function in both MSS and CSS.
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class FunctionClass extends MssClass {
    protected $name;
    protected $arguments;
    
    public function __construct($name, array $arguments = []) {
        $this->setName($name);
        $this->setArguments($arguments);
    }

    /**
     * Gets function's name
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Gets arguments for function
     * @return MssClass[]
     */
    public function getArguments() {
        return $this->arguments;
    }
    
    /**
     * Gets single funciton's argument if it exists and $default otherwise
     * @param mixed $id
     * @param mixed $default
     * @return MssClass
     */
    public function getArgument($id, $default = null) {
        return isset($this->arguments[$id]) ? $this->arguments[$id] : $default;
    }

    public function setName($name) {
        $this->name = trim($name);
        return $this;
    }

    public function setArguments(array $arguments) {
        switch ($this->getName()) {
            case 'url':
                $arguments = $this->parseArgumentsForUrlFunction($arguments);
                break;
            default:
                $arguments = array_map(function ($item) {
                    return MssClassHelper::parseMssClass($item, array('sequence'), true);
                }, $arguments);
                
        }
        $this->arguments = $arguments;
        return $this;
    }

    protected function parseArgumentsForUrlFunction(array $arguments) {
        $result = false;
        if (count($arguments) >= 1) {
            $firstArg = $arguments[0];
            if (!($firstArg instanceof MssClass)) {
                $result = DataUrlClass::parse($firstArg);
            }
            // parse with other registered classes
            if (!$result) {
                $result = MssClassHelper::parseMssClass($firstArg, array('sequence'), true);
            }
            if ($result instanceof MssClass) {
                return [$result];
            }
        }
        return [];
    }
    
    /**
     * Splits arguments parsed by StringHelper::parseFunction() method into array with respect to function name
     * @param array $functionInfo Info returned by StringHelper::parseFunction() method
     */
    protected static function splitFunctionArguments(array $functionInfo) {
        switch ($functionInfo['name']) {
            case 'url':
                $functionInfo['arguments'] = [$functionInfo['rawArgsString']];
                break;
            default:
                $functionInfo['arguments'] = StringHelper::parseFunctionArguments($functionInfo['rawArgsString'], true);
                break;
        }
        return $functionInfo;
    }
    
    protected function getModuleForFunction(VariableScope $vars) {
        $module = FunctionModuleHelper::findModule($this->getName(), $this->getArguments());
        if ($module !== false) {
            $module->setVars($vars);
        }
        return $module;
    }
    
    public function getValue(VariableScope $vars) {
        $module = $this->getModuleForFunction($vars);
        if ($module=== false) {
            throw new \MSSLib\Error\CompileException(null, 'FUNCTION_NOT_FOUND', [$this->getName()]);
        }
        
        return call_user_func_array([$module, $this->getName()], $this->getArguments());
    }

    protected function renderArguments(VariableScope $vars) {
        $arguments = [];
        switch ($this->getName()) {
            case 'url':
                $fileUrl = $this->getArgument(0);
                if ($fileUrl === null) {
                    break;
                }
                $fileUrl = $fileUrl->getValue($vars);
                if ($fileUrl instanceof \MSSLib\Essentials\MssClassInterfaces\IGenericString) {
                    $fileUrlStr = $fileUrl->getNonQuotedString();
                    $prefix = $this->getSetting('urlFunction.autoPrefix', false);
                    if (!filter_var($fileUrlStr, FILTER_VALIDATE_URL) && $this->getSetting('dataUrl.autoConvert')) {
                        $fileUrlStr =  $prefix ? $prefix . $fileUrlStr : $fileUrlStr;
                        $fullLocalPath = Document::makeRelativeFilePath(self::getRootObj()->getActiveDocument(), $fileUrlStr);
                        $fileInfo = new FileInfo($fullLocalPath);
                        if ($fileInfo->fileExists && $fileInfo->fileSize / 1024 <= $this->getSetting('dataUrl.sizeLimit', 0)) {
                            $arguments[] = DataUrlClass::fromFile($fullLocalPath, $fileInfo->mimeType);
                            break;
                        }
                        
                    }
                }
                $arguments[] = $fileUrl->toRealCss($vars);
                
                break;
            default:
                foreach ($this->getArguments() as $name=>$argument) {
                    $arguments[] = (is_string($name) ? $name . ' = ' : '') . $argument->toRealCss($vars);
                }
        }
        return  $this->getName() . '(' . implode(', ', $arguments) . ')';
    }
    
    public function toRealCss(VariableScope $vars) {
        $module = $this->getModuleForFunction($vars);
        if ($module=== false) {
            return $this->renderArguments($vars);
        }
        return call_user_func_array([$module, $this->getName()], $this->getArguments());
    }
    
        
    public static function parse(&$string) {
        $string_copy = $string;
        $functionInfo = StringHelper::parseFunction($string_copy, true, true, false);
        if (is_array($functionInfo)) {
            $string = $string_copy;
            $functionInfo = static::splitFunctionArguments($functionInfo);
            return new self($functionInfo['name'], $functionInfo['arguments']);
        }
        return false;
    }
}
