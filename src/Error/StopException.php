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

namespace MSSLib\Error;

/**
 * Description of StopException
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class StopException extends \Exception
{
    protected $result;
    
    public function __construct($result) {
        $this->result = $result;
    }
    
    public function getResult() {
        return $this->result;
    }



}
