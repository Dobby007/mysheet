<?php

/*
 *  Copyright 2014 Alexander Gilevich (alegil91@gmail.com)
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at 
 * 
 *      http://www.apache.org/licenses/LICENSE-2.0
 */

namespace MSSLib\Traits;

use MSSLib\Essentials\HandlerCallResult;

//require_once ROOTDIR . 'Helpers' . DS . 'HandlerCallResult' . EXT;

/**
 * Description of HandlerClassTrait
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
trait FireEventTrait
{
    private $_handlerMap = null;
    public function __call($name, array $arguments) {
        if (substr($name, -5) === 'Event') {
            $handlerName = substr($name, 0, -5);
        } else { 
            return;
        }
        
        
        $class = substr(strrchr(__CLASS__, '\\'), 1);
        if ($class === false) {
            $class = __CLASS__;
        }
        
        self::msInstance()->getEventRegistry()->triggerEvent($class, $handlerName, $arguments);
        
    }
}
