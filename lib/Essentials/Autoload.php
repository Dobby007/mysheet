<?php

namespace MySheet\Essentials;

use MySheet as MS;

/**
 * Description of Autoload
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class Autoload {
    
    public function autoload($class) {
        if (substr($class, 0, strlen(MSSNS) + 1) === MSSNS . '\\') {
            $class = substr($class, strlen(MSSNS) + 1);
            $file = str_replace(['\\', '/'], DS, $class);
            if (!file_exists(ROOTDIR . $file . MS\EXT)) {
                debug_print_backtrace();
            }
            require_once ROOTDIR . $file . MS\EXT;
        }
    }
    
    public function restoreAutoload() {
        spl_autoload_unregister([$this, 'autoload']);
        
    }
    
    public function registerAutoload() {
        spl_autoload_register([$this, 'autoload']);
    }
}
