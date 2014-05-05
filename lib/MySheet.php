<?php

namespace MySheet;

define('DS', DIRECTORY_SEPARATOR);
define('ROOTDIR', __DIR__ . DS);

require_once ROOTDIR . 'Autoload' . EXT;
require_once ROOTDIR . 'Functions' . EXT;

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
    
    public function __construct() 
    {
        $this->autoload = new Autoload();
    }

    public function parseFile($file) 
    {
        return $this->parseCode($code);
    }

    public function parseCode($code) 
    {
        $this->autoload->registerAutoload();
        
        $parser = new $this->parser($code);
        $result = $parser->comeon();
        
        $this->autoload->restoreAutoload();
        return $result;
    }
    
    public function registerPlugin($plugin)
    {
        if (is_string($plugin)) {
            
        }
        return $this;
    }
    
    public function registerPlugins($plugin0, $_plugins = null)
    {
        
    }

}
