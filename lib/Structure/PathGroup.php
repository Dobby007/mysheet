<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Structure;

/**
 * Class that allows rendering of MSS selectors to CSS ones.
 * It is used to present single MSS selector as a multiple CSS selectors (I called it "path" in this class)
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class PathGroup {
    private $paths = array();

    public function addPath($path) {
        $this->paths[] = $path;
        return $this;
    }
    
    public function getPaths() {
        return $this->paths;
    }
    
    public function setPaths(array $paths) {
        $this->paths = $paths;
    }
    
    public function alterPath($index, $path) {
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
