<?php

/*
 * Copyright 2015 Alexander Gilevich.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace MSSLib\Tools;

/**
 * Class that provides basic information about file
 *
 * @author Alexander Gilevich (dobby007)
 * @property-read string $mimeType MIME type of file
 * @property-read string $mimeEncoding Encoding of file provided by finfo_file function (e.g. binary)
 * @property-read string $fileSize Size of file in bytes
 * @property-read int $dateModified Date when file was modified
 * @property-read int $dateLastAccessed Time when file was accessed last time
 * @property-read int $dateCreated Time when file was created
 * @property-read string $directoryName Directory name of file
 * @property-read string $baseName File name with it's extension
 * @property-read string $fileName File name without extension
 * @property-read string $extension Extension of file
 * @property-read bool $fileExists Flag indicating that file exists in the system
 */
class FileInfo {
    private static $mimeDataBaseFile = null;
    private $filepath;
    private $mimeType;
    private $mimeEncoding;
    private $fileSize;
    private $dateModified;
    private $dateCreated;
    private $dateLastAccessed;
    private $directoryName;
    private $baseName;
    private $extension;
    private $fileName;
    private $fileExists;
    
    public static function getMimeDataBaseFile() {
        return self::$mimeDataBaseFile;
    }

    public static function setMimeDataBaseFile($mimeDataBaseFile) {
        self::$mimeDataBaseFile = $mimeDataBaseFile;
        return self;
    }

    public function __construct($filepath) {
        $this->filepath = $filepath;
        $this->fileExists = file_exists($filepath);
        if ($this->fileExists) {
            $this->readInfo();
        }
    }
    
    public function __get($name) {
        $method = 'get' . ucfirst($name);
        if (method_exists($this, $method)) {
            return $this->$method();
        } else {
            return $this->$name;
        }
    }
    
    protected function readInfo() {
        $this->readMimeInfo();
        $this->readFileStat();
        $this->readPathInfo();
    }
    
    protected function readMimeInfo() {
        $finfo = finfo_open(FILEINFO_MIME, self::$mimeDataBaseFile);

        if (!$finfo) {
            throw new RuntimeException('Opening mime-database failed!');
        }
        $mimeInfo = finfo_file($finfo, $this->filepath);
        if (preg_match('/^(.+)\;\s*charset=(.+)$/', $mimeInfo, $matches)) {
            $this->mimeType = $matches[1];
            $this->mimeEncoding = $matches[2];
            
        }
        
        finfo_close($finfo);
    }
    
    protected function readFileStat() {
        $stat = stat($this->filepath);
        $this->fileSize = $stat['size'];
        $this->dateCreated = $stat['ctime'];
        $this->dateModified = $stat['mtime'];
        $this->dateLastAccessed = $stat['atime'];
    }
    
    protected function readPathInfo() {
        $pathinfo = pathinfo($this->filepath);
        $this->directoryName = $pathinfo['dirname'];
        $this->baseName = $pathinfo['basename'];
        $this->extension = $pathinfo['extension'];
        $this->fileName = $pathinfo['filename'];
    }
    
    public function getMimeEncoding() {
        return $this->mimeEncoding;
    }
    
    public function getMimeType() {
        return $this->mimeType;
    }

    public function getFileSize() {
        return $this->fileSize;
    }
    
    public function getDateModified() {
        return $this->dateModified;
    }

    public function getDateCreated() {
        return $this->dateCreated;
    }

    public function getDateLastAccessed() {
        return $this->dateLastAccessed;
    }

    public function getDirectoryName() {
        return $this->directoryName;
    }

    public function getBaseName() {
        return $this->baseName;
    }

    public function getExtension() {
        return $this->extension;
    }

    public function getFilename() {
        return $this->fileName;
    }

    
}
