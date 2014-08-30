<?php

namespace MSSLib\Essentials;

use MSSLib as MSN;
use MSSLib\Tools\Debugger;

/**
 * Description of Autoload
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class Autoload
{
    
    public function autoload($class) {
        Debugger::logString("Trying to load: ". $class);
        if (substr($class, 0, strlen(MSSNS) + 1) === MSSNS . '\\') {
            $class = substr($class, strlen(MSSNS) + 1);
            $file = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $class);
            $filePath = MSN\WORKDIR . DIRECTORY_SEPARATOR . $file . MSN\EXT;
            if (!file_exists($filePath)) {
                throw new \Exception('Class not found: ' . $class);
            } else {
                include_once $filePath;
            }
        }
    }
    
    public function restoreAutoload() {
        Debugger::logString("MySheet Autoloader is gonna to be removed.");
        spl_autoload_unregister([$this, 'autoload']);
    }
    
    public function registerAutoload() {
        Debugger::logString("MySheet Autoloader is gonna to be registered.");
        spl_autoload_register([$this, 'autoload']);
    }
}
