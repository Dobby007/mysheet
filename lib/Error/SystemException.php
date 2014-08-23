<?php


namespace MSSLib\Error;

/**
 * Description of CompileException
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class SystemException extends MySheetException {
    public function __construct($category, $error_code, array $arguments = []) {
        parent::__construct(empty($category) ? 'System' : $category, $error_code, $arguments);
    }
    
    protected function updateArguments($arguments) {
        return $arguments;
    }
    
    public function getReason() {
        return I18N::translate($this->getCategory(), $this->getErrorCode(), $this->getArguments());
    }

}
