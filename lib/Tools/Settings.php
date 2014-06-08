<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Tools;

/**
 * Description of Settings
 *
 * @author dobby007
 * @property mixed $paramPriority The order in which parser would try to parse params in the rule
 */
class Settings {
    /*
    private $plugins = 'mixin';
    private $parserExtensions = 'ruleset';
    private $paramPriority;
    */
    public function __construct(array $settings = []) {
        $this->load($settings);
    }
    
    public function load(array $settings) {
        $default = require(ROOTDIR . 'Config/default' . EXT);
        $settings = array_merge($default, $settings);

        foreach ($settings as $name => $value) {
            $this->$name = $value;
        }
    }
    
    public function __get($name) {
        $method_name = 'get' . ucfirst($name);
        if (method_exists($this, $method_name)) {
            return $this->$method_name();
        } else {
            return $this->$name;
//            throw new Exception('Undefined property: $' . $name);
        }
    }
    
    public function __set($name, $value) {
        $method_name = 'set' . ucfirst($name);
        var_dump($method_name);
        if (method_exists($this, $method_name)) {
            return $this->$method_name($value);
        } else {
            $this->name = $value;
//            throw new Exception('Undefined property: $' . $name);
        }
    }
    
    protected function setParamPriority($paramPriority) {
        if (is_string($paramPriority))
            $paramPriority = preg_split ('/\s*,?\s+/', $paramPriority);
        
        if (is_array($paramPriority))   
            $this->paramPriority = $paramPriority;
        
    }

        
    /*
    public function __call($name, $arguments) {
        $method_prefix = substr($name, 0, 3);
        if ($method_prefix === 'get') {
            $property = lcfirst(substr($name, 3));
            return $this->$property;
        } else if ($method_prefix === 'set') {
            $property = lcfirst(substr($name, 3));
            $this->$property = reset($arguments);
            return $this;
        }
    }
    */
}
