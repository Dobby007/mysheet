<?php


namespace MSSLib\Error;

use MSSLib\Tools\I18N;

/**
 * Description of MySheetException
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
abstract class MySheetException extends \Exception {
    private $error_code;
    private $arguments;
    private $category;
    
    public function __construct($category, $error_code, array $arguments = []) {
        parent::__construct();
        $this->arguments = $arguments;
        $this->error_code = $error_code;
        $this->category = $category;
    }
    
    public function getErrorCode() {
        return $this->error_code;
    }

    public function getArguments() {
        return $this->updateArguments($this->arguments);
    }

    public function getCategory() {
        return $this->category;
    }

    /**
     * @return string Full exception message in a language provided in the settings
     */
    public function getReason() {
        return I18N::translate($this->getCategory(), $this->getErrorCode(), $this->getArguments());
    }
    
    protected abstract function updateArguments($arguments);
    
    public function __toString() {
        return (string)$this->getReason();
    }

}
