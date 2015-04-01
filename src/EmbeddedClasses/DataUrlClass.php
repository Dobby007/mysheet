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

namespace MSSLib\EmbeddedClasses;

use MSSLib\Essentials\MssClass;
use MSSLib\Essentials\VariableScope;
use MSSLib\Tools\FileInfo;
use MSSLib\Tools\Converters\DataEncoder;

/**
 * Class that represents a file data embedded right into CSS. E.g. data:image/png;base64,ABCDE=-
  *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class DataUrlClass extends MssClass {
    const DEFAULT_CHARSET = 'US-ASCII';
    const DEFAULT_MIME = 'text/plain';
    const DEFAULT_ENCODE_METHOD = 'text/plain';
    
    protected $_mime;
    protected $_data;
    protected $_encodeMethod;
    protected $_charset;
    
    public function __construct($mime, $data, $encodeMethod = 'base64', $charset = self::DEFAULT_CHARSET) {
        $this->setMime($mime);
        $this->setEncodeMethod($encodeMethod);
        $this->setData($data);
        $this->setCharset($charset);
    }

    public function getMime() {
        return $this->_mime;
    }

    public function getData() {
        return $this->_data;
    }
    
    public function getCharset() {
        return $this->_charset;
    }

    public function getEncodeMethod() {
        return $this->_encodeMethod;
    }

    public function setMime($mime) {
        $mime = trim($mime);
        $this->_mime = empty($mime) ? self::DEFAULT_MIME : $mime;
        
        return $this;
    }

    public function setData($data) {
        $this->_data = trim($data);
        return $this;
    }

    public function setEncodeMethod($encodeMethod = 'base64') {
        $encodeMethod = trim($encodeMethod);
        $this->_encodeMethod = empty($encodeMethod) ? self::DEFAULT_ENCODE_METHOD : $encodeMethod;
        return $this;
    }

    public function setCharset($charset) {
        $charset = trim($charset);
        $this->_charset = empty($charset) ? self::DEFAULT_CHARSET : $charset;
        return $this;
    }
    
    public function toRealCss(VariableScope $vars) {
        return sprintf('data:%s;%s,%s', $this->getMime(), $this->getEncodeMethod(), $this->getData());
    }
    
    public static function fromFile($filepath, $forcedMimeType = false) {
        $targetMimeType = $forcedMimeType;
        if (!$targetMimeType) {
            $fileInfo = new FileInfo($filepath);
            $targetMimeType = $fileInfo->mimeType;
        }
        $encodedContent = DataEncoder::b64EncodeFile($filepath);
        return new DataUrlClass($targetMimeType, $encodedContent);
    }
    
    public static function parse(&$string) {
        if (strncmp($string, 'data:', 5) !== 0) {
            return false;
        }
        
        if (preg_match('/^data:([a-z0-9-_.+\/]+)(?:;charset=(.+))?(?:;([a-z0-9]+))?,(\S+)/i', $string, $matches)) {
            parent::trimStringBy($string, strlen($matches[0]));
            return new self($matches[1], $matches[4], $matches[3], $matches[2]);
        }
        return false;
    }
}
