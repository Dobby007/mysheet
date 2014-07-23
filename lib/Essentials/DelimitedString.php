<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Essentials;

use MySheet\Helpers\StringHelper;

/**
 * Description of SeparatedString
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class DelimitedString {
    protected $list = array();
    protected $processCallback = null;
    
    public function __construct(array $list, callable $processCallback = null) {
        $this->setProcessCallback($processCallback);
        $this->setList($list);
    }
    
    public function getList() {
        return $this->list;
    }

    public function setList(array $list) {
        $this->list = array_map(array($this, 'processItem'), $list);
    }
    
    public function appendItem($item) {
        $this->list[] = $this->processItem($item);
    }
    
    public function prependItem($item) {
        array_unshift($this->list, $this->processItem($item));
    }
    
    public function implode($delimiter) {
        return implode($delimiter, $this->list);
    }
    
    public function setProcessCallback($processCallback) {
        $this->processCallback = $processCallback;
    }
    
    protected function processItem($item) {
        if (is_callable($this->processCallback)) {
            return call_user_func($this->processCallback, $item);
        }
        return $item;
    }
    
    public static function parseString(&$string, $separator, callable $processCallback = null) {
        $pieces = StringHelper::parseSplittedString($string, $separator);
        if (is_callable($processCallback)) {
            $pieces = array_map($processCallback, $pieces);
        }
        $result = new self($pieces);
        $result->setProcessCallback($processCallback);
        return $result;
    }
}
