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

namespace MSSLib\Essentials;

/**
 * Description of HandlerCallResult
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class HandlerCallResult {
    private $result, $handled;
    public function __construct($handled, $result = null) {
        $this->handled = !!$handled;
        $this->result= $result;
    }
    
    public function result() {
        return $this->result;
    }

    public function handled() {
        return $this->handled;
    }


}
