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
use MSSLib\Helpers\StringHelper;
use MSSLib\Helpers\MssClassHelper;
use MSSLib\Essentials\MssClass;

/**
 * Description of UrlFunctionRenderer
 *
 * @author dobby007
 */
class DefaultFunctionRenderer implements IFunctionRenderer
{
    public function parseArguments(array $arguments) {
        return array_map(function ($item) {
            return MssClassHelper::parseMssClass($item, array('sequence'), true);
        }, $arguments);
    }

    public function renderArguments(FunctionClass $function, VariableScope $vars) {
        $arguments = [];
        foreach ($function->getArguments() as $name=>$argument) {
            $arguments[] = (is_string($name) ? $name . ' = ' : '') . $argument->toRealCss($vars);
        }
        return $arguments;
    }

    public function splitFunctionArguments($rawArgsString) {
        return StringHelper::parseFunctionArguments($rawArgsString, true);
    }

}
