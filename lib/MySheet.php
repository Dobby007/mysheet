<?php

namespace MySheet;

define('DS', DIRECTORY_SEPARATOR);
define('ROOTDIR', __DIR__ . DS);


require_once ROOTDIR . 'tools/Parser'. EXT;

/**
 * Description of MySheet
 *
 * @author dobby007
 */
class MySheet {
   public $parser = 'MySheet\Tools\Parser';
   public $cacher = 'Cacher';
   
   public function parseFile($file) {
       return $this->parseCode($code);
   }
   
   public function parseCode($code) {
       $parser = new $this->parser($code);
       
       return $parser->comeon();
   }
   
}
