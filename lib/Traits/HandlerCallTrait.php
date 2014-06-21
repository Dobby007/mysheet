<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Traits;

use MySheet\Essentials\HandlerCallResult;

//require_once ROOTDIR . 'Helpers' . DS . 'HandlerCallResult' . EXT;

/**
 * Description of HandlerClassTrait
 *
 * @author dobby007
 */
trait HandlerCallTrait {
    private $_handlerMap = null;
    public function __call($name, array $arguments) {
        if (substr($name, -5) === 'Event')
            $handlerName = substr($name, 0, -5);
        else return;
        
        $class = substr(strrchr(__CLASS__, '\\'), 1);
        if ($class === false) {
            $class = __CLASS__;
        }
        
        $result = $this->getRoot()->getHandlerFactory()->triggerEvent($class, $handlerName, $arguments, $handled);
        
        return new HandlerCallResult($handled, $result);
        
    }
}
