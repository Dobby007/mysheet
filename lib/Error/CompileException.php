<?php


namespace MySheet\Error;

/**
 * Description of CompileException
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class CompileException extends MySheetException {
    public function __construct($error_code, array $arguments = []) {
        parent::__construct($error_code, $arguments);
    }


}
