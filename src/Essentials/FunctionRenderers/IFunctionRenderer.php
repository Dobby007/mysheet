<?php

/*
 * Copyright 2015 dobby007.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace MSSLib\Essentials\FunctionRenderers;

use MSSLib\Essentials\VariableScope;
use MSSLib\EmbeddedClasses\FunctionClass;

/**
 * Description of IFunctionRenderer
 *
 * @author dobby007
 */
interface IFunctionRenderer
{
    /**
     * Splits raw string of arguments parsed by StringHelper::parseFunction() method into array
     * @param string $rawArgsString Raw string of arguments
     * @return string[] Array of arguments
     */
    function splitFunctionArguments($rawArgsString);
    /**
     * Parses given array of string arguments into array of correspondent MssClasses
     */
    function parseArguments(array $arguments);
    /**
     * Compiles arguments of given function into CSS
     */
    function renderArguments(FunctionClass $function, VariableScope $vars);
}
