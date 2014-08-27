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

use MSSLib\Helpers\StringHelper;

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
