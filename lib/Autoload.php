<?php

namespace MySheet;

/**
 * Description of Autoload
 *
 * @author dobby007
 */
class Autoload {
    
    public function autoload($class)
    {
        
        if (substr($class, 0, strlen(__NAMESPACE__) + 1) === __NAMESPACE__ . '\\')
        {
            $class = substr($class, strlen(__NAMESPACE__) + 1);
//            $file = strtolower($class);
            $file = str_replace(['\\', '/'], DS, $class);
            require_once ROOTDIR . $file . EXT;
//            print '[[' . $class . ']]';
            
        }
    }
    
    public function restoreAutoload()
    {
        spl_autoload_unregister([$this, 'autoload']);
    }
    
    public function registerAutoload() 
    {
        spl_autoload_register([$this, 'autoload']);
    }
}
