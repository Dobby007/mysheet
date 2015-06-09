<?php


namespace MSSLib\Error;

use MSSLib\Tools\I18N;

/**
 * Description of ParseException
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class ParseException extends MySheetException {

    /**
     * @param string $category
     * @param int $error_code
     * @param array $arguments
     *
     * Standard error codes: BAD_SELECTOR, BAD_DECLARATION, BAD_INDENTATION, PARENT_NOT_FOUND, UNRECOGNIZED_SEQUENCE, PARAM_NOT_PARSED, BAD_VARIABLE_NAME, BAD_DEFINITION, WRONG_ARGUMENTS_COUNT
     */
    public function __construct($category, $error_code, array $arguments = []) {
        parent::__construct(empty($category) ? 'Parsing' : $category, $error_code, $arguments);
    }
    
    protected function updateArguments($arguments) {
        return $arguments;
    }
    
    public function getReason() {
        $text = I18N::translate($this->getCategory(), $this->getErrorCode(), $this->getArguments());
        if (!$text) {
            return parent::getReason();
        }
        return $text;
    }
    
}