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


namespace MSSLib;

const DS = DIRECTORY_SEPARATOR;
const EXT = '.php';

define('MSSNS', __NAMESPACE__);
define(MSSNS . '\WORKDIR', __DIR__ . DS);


require_once WORKDIR . DS . 'Essentials' . DS . 'IMSSettings' . EXT;
require_once WORKDIR . DS . 'Essentials' . DS . 'Autoload' . EXT;
require_once WORKDIR . DS . 'Tools' . DS . 'Debugger' . EXT;

use MSSLib\Essentials\Autoload;
use MSSLib\Essentials\HandlerFactory;
use MSSLib\Essentials\IParser;
use MSSLib\Essentials\IMSSettings;
use MSSLib\Essentials\MSSettings;
use MSSLib\Essentials\FuncListManager;
use MSSLib\Essentials\VariableScope;
use MSSLib\Tools\I18N;
use MSSLib\Error\InputException;
use MSSLib\Essentials\TypeClassReference;
use MSSLib\Structure\Document;

/**
 * Description of MySheet
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class MySheet
{

    const WORKDIR = __DIR__;

    /**
     * @var IParser Reference to parser object
     */
    public $parser = null;
    public $cacher = null;

    /** @var Autoload */
    private $_autoload;
    private $_hf;
    private $_flm;
    private $_vs;
    private $_activeDocument;
    private $_activeDirectory;
    protected $settings;
    protected $plugins = array();
    protected $_parsedFilePath = null;
    
    private function __construct() {
        $this->_autoload = new Autoload();
    }

    public static function Instance() {
        static $instance;
        if ($instance === null) {
            $instance = new self();
        }
        return $instance;
    }

    public function init(IMSSettings $settings = null) {
        try {
            $this->_autoload->registerAutoload();
            if (!$settings) {
                $settings = new MSSettings();
            }
            $this->setSettings($settings);
            $this->initDependecies();
            I18N::setLanguage($this->getSettings()->language);
            $parser = $this->getSettings()->parser;
            $parserObj = new $parser(null);
            if (!($parserObj instanceof IParser)) {
                throw new SystemException(null, 'BAD_PARSER');
            } else {
                $this->parser = $parserObj;
            }
            $this->_hf = new HandlerFactory();
            $this->_vs = new VariableScope();
            $this->getHandlerFactory()->registerHandler('Block', 'cssRenderingEnded', function () {
                $this->getVars()->clean();
            });
            $this->_flm = new FuncListManager();
            $this->initMssClasses();
            $this->initExtensions();
            $this->initOperators();
            $this->initFunctionModules();
            $this->initPlugins();
            
            $this->setRightOrder();
        } catch (\Exception $exc) {
            throw $exc;
        }
        return $this;
    }

    protected function initPlugins() {
        $this->plugins = [];

        foreach ($this->getSettings()->plugins as $key => $value) {
            if (is_string($value)) {
                $this->registerPlugin($value);
            } else if (is_array($value)) {
                $this->registerPlugin($key, $value);
            }
        }
    }
    
    protected function initFunctionModules() {
        $fmNs = 'MSSLib\\EmbeddedFunctions\\';
        foreach ($this->getSettings()->functionModules as $functionModuleClass) {
            $class = $fmNs . ucfirst($functionModuleClass) . 'Module';
            if (class_exists($class)) {
                $this->getListManager()->getList('FunctionModule')->addFunctional(new $class);
            }
        }
    }

    protected function initDependecies() {
        foreach ($this->getSettings()->dependencies as $depFile) {
            if ($depFile === false) {
                continue;
            }
            require_once MySheet::WORKDIR . \MSSLib\DS . $depFile;
        }
    }
    
    protected function initExtensions() {
        $peNs = 'MSSLib\\ParserExtensions\\';
        foreach ($this->getSettings()->parserExtensions as $peClass) {
            $class = $peNs . ucfirst($peClass) . 'ParserExtension';
            $this->parser->addParserExtension($class);
        }
    }

    protected function initMssClasses() {
        $availableParams = require(self::WORKDIR . DS . 'Etc' . DS . 'Includes' . DS . 'EmbeddedClasses' . EXT);
        foreach ($availableParams as $paramClass) {
            $classRef = new TypeClassReference($paramClass, 'Class', 'MSSLib\\EmbeddedClasses');
            if ($classRef->classExists()) {
                $this->getListManager()->getList('MssClass')->addFunctional($classRef);
                
                $implementedInterfaces = class_implements($classRef->getFullClass());
                if (isset($implementedInterfaces['MSSLib\Essentials\Math\IOperatorRegistrar'])) {
                    $class = $classRef->getFullClass();
                    $class::registerOperations();
                }
            }
        }
    }
    
    protected function setRightOrder() {
        $this->getListManager()->getList('MssClass')->setOrder(array_map(function ($mssClass) {
            return strtolower($mssClass);
        }, $this->getSettings()->mssClasses), null, function (TypeClassReference $mssClassRef) {
            return strtolower($mssClassRef->getShortName());
        });
    }
    
    protected function initOperators() {
        $availableOperators = require(self::WORKDIR . DS . 'Etc' . DS . 'Includes' . DS . 'OperatorsPrecedence' . EXT);
        $this->processOperatorsGroup($availableOperators);
    }
    
    private function processOperatorsGroup($operatorsGroup) {
        foreach ($operatorsGroup as $item) {
            if (is_string($item)) {
                $classRef = new TypeClassReference(ucfirst($item), 'Operator', 'MSSLib\\Operators');
                if ($classRef->classExists()) {
                    $this->getListManager()->getList('Operator')->addFunctional($classRef);
                }
            } else if (is_array($item)) {
                $this->processOperatorsGroup($item);
            }
        }
    }

    public function parseFile($file) {
        $autoload_enabled = $this->getSettings()->get('system.internal_autoload', false);
        if (is_file($file)) {
            $this->_parsedFilePath = realpath($file);
            return $this->parseCode(file_get_contents($this->_parsedFilePath));
        } else {
            if ($autoload_enabled) {
                $this->getAutoload()->registerAutoload();
            }
            
            throw new InputException(null, 'FILE_NOT_FOUND', [$file]);
        }
        return null;
    }

    public function parseCode($code) {
        $autoload_enabled = $this->getSettings()->get('system.internal_autoload', false);
        if ($autoload_enabled) {
            $this->_autoload->registerAutoload();
        }

        $this->parser->setCode($code);
        $result = $this->parser->comeon();

        return $result;
    }

    public function parseImportFile($file) {
        $paths = $this->getSettings()->get('import.paths', []);
        if (!is_array($paths)) {
            $paths = [$paths];
        }
        
        Helpers\ArrayHelper::append($paths, dirname($this->_parsedFilePath));
        foreach ($paths as $path) {
            if (!is_string($path)) {
                continue;
            }

            $fullpath = $path . DS . $file;
            if (is_file($fullpath)) {
                return $this->parseFile($fullpath);
            }
        }

        return false;
    }

    public function registerPlugin($plugin, array $settings = []) {
        if (is_string($plugin)) {
            $pluginClass = $plugin;
            if (strpos($pluginClass, '\\') === false) {
                $pluginClass = ucfirst($plugin);
                $pluginClass = '\\MSSLib\Plugins\\' . $pluginClass . '\Plugin' . $pluginClass;
            }
            
            if (class_exists($pluginClass)) {
                /* @var $pi \MySheet\Plugins\PluginBase */
                $pi = new $pluginClass();
                foreach ($settings as $name => $value) {
                    $pi->$name = $value;
                }
                $pi->init();
                $this->plugins[$plugin] = $pi;
            }
        }
        return $this;
    }

    public function registerPlugins($plugin0, $_plugins = null) {
        foreach (func_get_args() as $arg) {
            $this->registerPlugin($arg);
        }
    }

    /**
     * @return IMSSettings Instance of MSSettings class
     */
    public function getSettings() {
        return $this->settings;
    }

    public function setSettings(IMSSettings $settings) {
        $this->settings = $settings;
    }

    
    
    /**
     * @return HandlerFactory Instance of HandlerFactory class
     */
    public function getHandlerFactory() {
        return $this->_hf;
    }

    /**
     * @return IParser Instance of HandlerFactory class
     */
    public function getParser() {
        return $this->parser;
    }

    /**
     * @return Autoload Instance of Autoload class
     */
    public function getAutoload() {
        return $this->_autoload;
    }

    /**
     * @return FuncListManager Instance of FuncListManager class
     */
    public function getListManager() {
        return $this->_flm;
    }

    /**
     * @return VariableScope
     */
    public function getVars() {
        return $this->_vs;
    }
    
    /**
     * Gets document currently being proccessed
     * @return Document
     */
    public function getActiveDocument() {
        return $this->_activeDocument;
    }

    /**
     * Sets document currently being proccessed
     * @return Document
     */
    public function setActiveDocument(Document $activeDocument = null) {
        $this->_activeDocument = $activeDocument;
        return $this;
    }
    
    public function getActiveDirectory() {
        return $this->_activeDirectory;
    }

    public function setActiveDirectory($activeDirectory) {
        $this->_activeDirectory = $activeDirectory;
        return $this;
    }
    
    public static function setDebugMode($debugMode) {
        return Tools\Debugger::setDebugMode($debugMode);
    }

}