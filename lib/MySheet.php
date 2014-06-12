<?php

namespace MySheet;

define('DS', DIRECTORY_SEPARATOR);
define('ROOTDIR', __DIR__ . DS);
define('MSSNS', 'MySheet');

require_once ROOTDIR . 'Essentials' . DS . 'Autoload' . EXT;

use MySheet\Essentials\Autoload;
use MySheet\Helpers\HandlerFactory;
use MySheet\Tools\IParser;
use MySheet\Tools\Settings;
use MySheet\Essentials\FuncListManager;
use MySheet\Essentials\RuleParam;
use MySheet\Essentials\VariableScope;

/**
 * Description of MySheet
 *
 * @author dobby007
 */
class MySheet 
{
    
    /**
     * @var IParser Reference to parser object
     */
    public $parser = 'MySheet\Tools\Parser';
    public $cacher = 'Cacher';
    
    
    /** @var Autoload */
    private $autoload;
    private $hf;
    private $flm;
    private $vs;
    
    protected $settings;
    protected $plugins = array();


    public function __construct() {
        $this->autoload = new Autoload();
        
        $this->autoload->registerAutoload();
        $this->setSettings(new Settings());
        $this->autoload->restoreAutoload();
        
        $this->init();
    }
    
    public function init() {
        $this->autoload->registerAutoload();
        $this->parser = new $this->parser(null, $this);
        $this->hf = new HandlerFactory();
        $this->vs = new VariableScope();
        $this->vs->setRoot($this);
        $this->getHandlerFactory()->registerHandler('Block', 'cssRenderingEnded', function() {
            $this->getVars()->clean();
        });
        $this->flm = new FuncListManager();
        
        $ruleParamNs = 'MySheet\\Functionals\\RuleParam\\';
        foreach ($this->getSettings()->paramPriority as $paramClass) {
            $class = $ruleParamNs . ucfirst($paramClass) . 'Param';
            $this->getListManager()->addFunctional('RuleParam', $class);
        }
        
        $this->initPlugins();
        $this->initExtensions();
        $this->autoload->restoreAutoload();
    }
    
    protected function initPlugins() {
        $this->plugins = [];
        
        $this->registerPlugin('Mixin');
    }
    
    protected function initExtensions() {
        $this->parser->addParserExtension('\MySheet\Internal\ParserExtensions\RulesetParserExtension');
    }
    
    public function parseFile($file) {
        return $this->parseCode($code);
    }

    public function parseCode($code) {
        $this->autoload->registerAutoload();
        $this->parser->setCode($code);
        $result = $this->parser->comeon();
        
        $this->autoload->restoreAutoload();
        return $result;
    }
    
    public function registerPlugin($plugin) {
        if (is_string($plugin)) {
            $plugin = ucfirst($plugin);
            $pluginClass = '\MySheet\Plugins\\' . $plugin . '\Plugin' . $plugin;
            if (class_exists($pluginClass)) {
                /* @var $pi \MySheet\Plugins\PluginBase */
                $pi = new $pluginClass($this);
                $pi->init();
                $this->plugins[$plugin] = $pi;
            }
        }
        return $this;
    }
    
    public function registerPlugins($plugin0, $_plugins = null) {
        
    }
    
    
    /**
     * @return Settings Instance of Settings class
     */
    public function getSettings() {
        return $this->settings;
    }

    public function setSettings(Settings $settings) {
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
