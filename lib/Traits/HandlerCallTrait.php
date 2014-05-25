<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Traits;

use MySheet\Helpers\HandlerCallResult;

require_once ROOTDIR . 'Helpers' . DS . 'HandlerCallResult' . EXT;

/**
 * Description of HandlerClassTrait
 *
 * @author dobby007
 */
trait HandlerCallTrait {
    private $_handlerMap = null;
    public function __call($name, array $arguments) {
        if (substr($name, -7) === 'Handler')
            $handlerName = substr($name, 0, -7);
        else return;
        
        $class = substr(strrchr(get_class(), '\\'), 1);
        if ($class === false) {
            $class = get_class();
        }
        
        $result = $this->getRoot()->getHandlerFactory()->executeHandler($class, $handlerName, $arguments, $handled);
        
        return new HandlerCallResult($handled, $result);
        
    }
}
