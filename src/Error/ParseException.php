<?php


namespace MSSLib\Error;

use MSSLib\Tools\I18N;

/**
 * Description of ParseException
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class ParseException extends MySheetException {
    
    public function __construct($category, $error_code, array $arguments = []) {
        parent::__construct(empty($category) ? 'Parsing' : $category, $error_code, $arguments);
    }
    
    protected function updateArguments($arguments) {
        return $arguments;
    }
    
    public function getReason() {
        return I18N::translate($this->getCategory(), $this->getErrorCode(), $this->getArguments());
    }
    
}