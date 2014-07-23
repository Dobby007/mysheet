<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Essentials;

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
