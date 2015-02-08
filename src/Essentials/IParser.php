<?php

namespace MSSLib\Essentials;

/**
 * Interface for all parsers
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
interface IParser 
{

    function setCode($code);

    function addParserExtension($extension);

    function comeon();
}
