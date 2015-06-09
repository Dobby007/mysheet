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
use MSSLib\Essentials\FunctionRenderers\IFunctionRenderer;

/**
 * Internal class that allows using functions in rule values. It is a rule parameter (MssClass).
 * It represents a function in both MSS and CSS.
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class FunctionClass extends MssClass
{
    protected $name;
    protected $arguments;
    /**
     *
     * @var IFunctionRenderer Function Renderer for current function
     */
    protected $_functionRenderer;
    /**
     * @var array Optional data for this function instance
     */
    public $data = array();
    
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
     * Gets single function's argument if it exists and $default otherwise
     * @param mixed $id
     * @param mixed $default
     * @return MssClass
     */
    public function getArgument($id, $default = null) {
        return isset($this->arguments[$id]) ? $this->arguments[$id] : $default;
    }

    public function setName($name) {
        $this->name = trim($name);
        $this->_functionRenderer = self::getFunctionRenderer($name);
        return $this;
    }

    public function setArguments(array $arguments) {
        $this->arguments = $this->_functionRenderer->parseArguments($this, $arguments);
        return $this;
    }
    
    /**
     * Splits arguments parsed by StringHelper::parseFunction() method into array with respect to function name
     * @param array $functionInfo Info returned by StringHelper::parseFunction() method
     */
    protected static function splitFunctionArguments(array $functionInfo) {
        $functionInfo['arguments'] = self::getFunctionRenderer($functionInfo['name'])->splitFunctionArguments($functionInfo['rawArgsString']);
        return $functionInfo;
    }
    
    protected function getModuleForFunction(VariableScope $vars) {
        $module = FunctionModuleHelper::findModule($this->getName(), $this->getArguments());
        if ($module !== false) {
            $module->setVars($vars);
        }
        return $module;
    }
    
    public function executeFunction(VariableScope $vars) {
        $module = $this->getModuleForFunction($vars);
        if ($module === false) {
            throw new \MSSLib\Error\CompileException(null, 'FUNCTION_NOT_FOUND', [$this->getName()]);
        }
        return call_user_func_array([$module, $this->getName()], $this->getArguments());
    }
    
    public function getValue(VariableScope $vars) {
        $module = $this->getModuleForFunction($vars);
        if ($module === false) {
            return $this;
        }
        $arguments = $this->_functionRenderer->prepareArguments($this, $this->getArguments());
        /* @var MssClass $result */
        return call_user_func_array([$module, $this->getName()], $arguments);
    }

    protected function renderArguments(VariableScope $vars) {
        $arguments = $this->_functionRenderer->renderArguments($this, $vars);
        if (empty($arguments)) {
            $arguments = [];
        }
        return  $this->getName() . '(' . implode(', ', $arguments) . ')';
    }
    
    /**
     * Returns associated renderer for the function
     * @staticvar array $renderers
     * @param string $name Name of the function
     * @return IFunctionRenderer
     */
    protected static function getFunctionRenderer($name) {
        static $renderers = [];
        if (empty($renderers)) {
            $renderers['url'] = new \MSSLib\Essentials\FunctionRenderers\UrlFunctionRenderer();
            $renderers['changeColor'] = new \MSSLib\Essentials\FunctionRenderers\ChangeColorRenderer();
            $renderers['default'] = new \MSSLib\Essentials\FunctionRenderers\DefaultFunctionRenderer();
        }
        if (isset($renderers[$name]) && $name !== 'default') {
            return $renderers[$name];
        } else {
            switch ($name) {
                case '-o-linear-gradient':
                case '-ms-linear-gradient':
                case '-webkit-linear-gradient':
                case '-moz-linear-gradient':
                case 'linear-gradient':
                    /** @todo Implement FunctionRenderer to improve performance and efficiency */
                    break;
            }
            
        }
        return $renderers['default'];
    }
    
    public function toRealCss(VariableScope $vars) {
        $module = $this->getModuleForFunction($vars);
        if ($module=== false) {
            return $this->renderArguments($vars);
        }
        $arguments = $this->_functionRenderer->prepareArguments($this, $this->getArguments());
        /* @var MssClass $result */
        $result = call_user_func_array([$module, $this->getName()], $arguments);
        return $result->toRealCss($vars);
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

