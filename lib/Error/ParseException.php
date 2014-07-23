<?php


namespace MySheet\Error;

/**
 * Description of ParseException
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class ParseException extends \Exception {
    private $error_code;
    
    private $arguments;
    
    public function __construct($error_code, array $arguments = []) {
        parent::__construct();
        $this->arguments = $arguments;
        $this->error_code = $error_code;
    }
    
    public function getErrorCode() {
        return $this->error_code;
    }

    public function getArguments() {
        return $this->arguments;
    }




}
