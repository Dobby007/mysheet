<?php


namespace MySheet\Error;

/**
 * Description of ParseException
 *
 * @author dobby007
 */
class ParseException extends \Exception {
    private $error_code;
    
    public function __construct($error_code) {
        parent::__construct();
        
        $this->error_code = $error_code;
    }
    
    public function getErrorCode() {
        return $this->error_code;
    }




}
