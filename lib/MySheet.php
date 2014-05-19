<?php

namespace MySheet;

define('DS', DIRECTORY_SEPARATOR);
define('ROOTDIR', __DIR__ . DS);
define('MSSNS', 'MySheet');

require_once ROOTDIR . 'Essentials' . DS . 'Autoload' . EXT;
require_once ROOTDIR . 'Functions' . EXT;

use MySheet\Essentials\Autoload;
use MySheet\Helpers\HandlerFactory;
use MySheet\Tools\IParser;

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
    
    protected $plugins = array();
    protected $hf;


    public function __construct() {
        $this->autoload = new Autoload();
        $this->autoload->registerAutoload();
        
        $this->parser = new $this->parser(null, $this);
        $this->initPlugins();
        $this->initExtensions();
        
        $this->autoload->restoreAutoload();
    }
    
    protected function initPlugins() {
        $this->plugins = [];
        $this->hf = new HandlerFactory();
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



}
