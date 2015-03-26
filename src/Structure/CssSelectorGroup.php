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

namespace MSSLib\Structure;

/**
 * Class that allows rendering of MSS selectors to CSS ones.
 * It is used to present single MSS selector as a multiple CSS selectors (I called it "path" in this class)
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class CssSelectorGroup {
    private $paths = array();

    public function addSelector($path) {
        $this->paths[] = $path;
        return $this;
    }
    
    public function getSelectors() {
        return $this->paths;
    }
    
    public function setSelectors(array $paths) {
        $this->paths = $paths;
    }
    
    public function alterSelector($index, $path) {
        if (isset($this->paths[$index])) {
            $this->paths[$index] = $path;
        }
    }

    public function join($separator = ', ') {
        return implode($separator, $this->paths);
    }
    
    public function __toString() {
        return $this->join();
    }

}
