<?php

namespace MSSLib;

const DS = DIRECTORY_SEPARATOR;
const EXT = '.php';
const WORKDIR = __DIR__;

define('MSSNS', 'MSSLib');

require_once WORKDIR . DS . 'Essentials' . DS . 'Autoload' . EXT;

use MSSLib\Essentials\Autoload;
use MSSLib\Essentials\HandlerFactory;
use MSSLib\Tools\IParser;
use MSSLib\Tools\MSSettings;
use MSSLib\Essentials\FuncListManager;
use MSSLib\Essentials\VariableScope;
use MSSLib\Helpers\StringHelper;
use MSSLib\Tools\I18N;

/**
 * Description of MySheet
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class MySheet {

    const WORKDIR = __DIR__;

    /**
     * @var IParser Reference to parser object
     */
    public $parser = null;
    public $cacher = null;

    /** @var Autoload */
    private $autoload;
    private $hf;
    private $flm;
    private $vs;
    protected $settings;
    protected $plugins = array();

    private function __construct() {
        $this->autoload = new Autoload();

        $this->autoload->registerAutoload();
        $this->setSettings(new MSSettings());
        $this->autoload->restoreAutoload();
    }

    public static function Instance() {
        static $instance;
        if ($instance === null) {
            $instance = new self();
            $instance->init();
        }
        return $instance;
    }

    public function init() {
        try {
            $this->autoload->registerAutoload();
            I18N::setLanguage($this->getSettings()->language);
            $parser = $this->getSettings()->parser;
            $parserObj = new $parser(null);
            if (!($parserObj instanceof IParser)) {
                throw new SystemException(null, 'BAD_PARSER');
            } else {
                $this->parser = $parserObj;
            }
            $this->hf = new HandlerFactory();
            $this->vs = new VariableScope();
            $this->getHandlerFactory()->registerHandler('Block', 'cssRenderingEnded', function () {
                $this->getVars()->clean();
            });
            $this->flm = new FuncListManager();
            $this->initRuleParams();
            $this->initPlugins();
            $this->initExtensions();
            $this->getListManager()->getList('RuleParam')->setOrder($this->getSettings()->ruleParams, function ($orderItem, $origItem) {
                if (!is_string($orderItem) || !is_string($origItem)) {
                    return;
                }
                $origItem = StringHelper::rtrimBySubstring(StringHelper::getClassName($origItem), 'Param');
                return $origItem === ucfirst($orderItem);
            });
            $this->autoload->restoreAutoload();
        } catch (\Exception $exc) {
            $this->autoload->restoreAutoload();
            throw $exc;
        }
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

    protected function initExtensions() {
        $peNs = '\\MSSLib\\ParserExtensions\\';
        foreach ($this->getSettings()->parserExtensions as $peClass) {
            $class = $peNs . ucfirst($peClass) . 'ParserExtension';
            $this->parser->addParserExtension($class);
        }
    }

    protected function initRuleParams() {
        $availableParams = require_once('Config' . DS . 'RuleParams' . EXT);
        $ruleParamNs = '\\MSSLib\\Functionals\\RuleParam\\';
        foreach ($availableParams as $paramClass) {
            $class = $ruleParamNs . ucfirst($paramClass) . 'Param';
            $this->getListManager()->getList('RuleParam')->addFunctional($class);
        }
    }

    public function parseFile($file, $autoload = true) {
        if (is_file($file)) {
            return $this->parseCode(file_get_contents($file), $autoload);
        }
        return null;
    }

    public function parseCode($code, $autoload = true) {
        if ($autoload) {
            $this->autoload->registerAutoload();
        }

        $this->parser->setCode($code);
        $result = $this->parser->comeon();

        if ($autoload) {
            $this->autoload->restoreAutoload();
        }

        return $result;
    }

    public function parseImportFile($file) {
        $paths = $this->getSettings()->get('import.paths', []);
        foreach ($paths as $path) {
            if (!is_string($path)) {
                continue;
            }

            $fullpath = $path . DS . $file;
            if (is_file($fullpath)) {
                var_dump($fullpath);
                return $this->parseFile($fullpath, false);
            }
        }

        return false;
    }

    public function registerPlugin($plugin, array $settings = []) {
        if (is_string($plugin)) {
            $plugin = ucfirst($plugin);
            $pluginClass = '\\MSSLib\Plugins\\' . $plugin . '\Plugin' . $plugin;
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
        
    }

    /**
     * @return MSSettings Instance of MSSettings class
     */
    public function getSettings() {
        return $this->settings;
    }

    public function setSettings(MSSettings $settings) {
        $this->settings = $settings;
    }

    /**
     * @return HandlerFactory Instance of HandlerFactory class
     */
    public function getHandlerFactory() {
        return $this->hf;
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
        return $this->autoload;
    }

    /**
     * @return FuncListManager Instance of FuncListManager class
     */
    public function getListManager() {
        return $this->flm;
    }

    /**
     * @return VariableScope
     */
    public function getVars() {
        return $this->vs;
    }

}
