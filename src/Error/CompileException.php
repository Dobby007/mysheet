<?php


namespace MSSLib\Error;

use MSSLib\Tools\I18N;

/**
 * Description of CompileException
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class CompileException extends MySheetException {
    public function __construct($category, $error_code, array $arguments = []) {
        parent::__construct(empty($category) ? 'Compiling' : $category, $error_code, $arguments);
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
