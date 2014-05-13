<?php

namespace MySheet;

define('DS', DIRECTORY_SEPARATOR);
define('ROOTDIR', __DIR__ . DS);

require_once ROOTDIR . 'Autoload' . EXT;
require_once ROOTDIR . 'Functions' . EXT;
require_once ROOTDIR . 'HandlerFactory' . EXT;


/**
 * Description of MySheet
 *
 * @author dobby007
 */
class MySheet 
{

    public $parser = 'MySheet\Tools\Parser';
    public $cacher = 'Cacher';
    
    
    /** @var Autoload */
    private $autoload;
    
    protected $plugins = array();
    protected $hf;


    public function __construct() 
    {
        $this->autoload = new Autoload();
        $this->hf = new HandlerFactory();
        
        $this->autoload->registerAutoload();
        $this->registerPlugin('Mixin');
        $this->autoload->restoreAutoload();
    }

    public function parseFile($file) 
    {
        return $this->parseCode($code);
    }

    public function parseCode($code) 
    {
        $this->autoload->registerAutoload();
        
        $parser = new $this->parser($code, $this);
        $result = $parser->comeon();
        
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
    
    public function registerPlugins($plugin0, $_plugins = null)
    {
        
    }
    
    /**
     * @return HandlerFactory Instance of HandlerFactory class
     */
    public function getHandlerFactory() {
        return $this->hf;
    }



}
