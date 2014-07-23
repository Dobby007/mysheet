<?php

namespace MySheet\Tools;

use MySheet\Essentials\ParserExtension;

/**
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
interface IParser {
    public function comeon();
    public function addParserExtension($extension);

    public function getCode();
    public function setCode($code);
}
