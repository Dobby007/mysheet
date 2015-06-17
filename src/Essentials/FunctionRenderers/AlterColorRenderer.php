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

use MSSLib\EmbeddedClasses\ColorClass;
use MSSLib\Error\ParseException;
use MSSLib\Essentials\VariableScope;
use MSSLib\EmbeddedClasses\FunctionClass;
use MSSLib\Helpers\StringHelper;

/**
 * FunctionRenderer for changeColor MSS function
 *
 * @author dobby007
 */
class AlterColorRenderer implements IFunctionRenderer
{
    use \MSSLib\Traits\RootClassTrait;

    /**
     * @inheritdoc
     */
    public function parseArguments(FunctionClass $function, array $arguments) {
        $result = false;
        if (count($arguments) > 0) {
            $firstArg = $arguments[0];
            $color = ColorClass::parse($firstArg);
            if (!$color) {
                throw new ParseException('Parsing.color', 'CHANGECOLOR_WRONG_ARGUMENTS');
            }

            $modifiers = explode('>', $firstArg);
            $modifiers = array_map(function ($modifier){
                $parsedModifier = StringHelper::parseFunction($modifier);
                if (!$parsedModifier) {
                    return false;
                }
                $metricVal = StringHelper::parseMetric($parsedModifier['rawArgsString']);
                if (!$metricVal) {
                    return false;
                }
                return [
                    'name' => $parsedModifier['name'],
                    'value' => $metricVal
                ];
            }, $modifiers);
            $function->data['colorModifiers'] = array_filter($modifiers);
            return [$color];
        } else {
            throw new ParseException(null, 'WRONG_ARGUMENTS_COUNT', [1, 0]);
        }
        return [];
    }

    /**
     * @inheritdoc
     */
    public function renderArguments(FunctionClass $function, VariableScope $vars) {
        $color = $function->getArgument(0);
        return [$color->toRealCss($vars)];
    }

    /**
     * @inheritdoc
     */
    public function splitFunctionArguments($rawArgsString) {
        return [$rawArgsString];
    }

    /**
     * @inheritdoc
     */
    public function prepareArguments(FunctionClass $function, array $arguments)
    {
        if (count($arguments) > 0) {
            $arguments = [$arguments[0], $function->data['colorModifiers']];
        }
        return $arguments;
    }

    /**
     * @inheritdoc
     */
    function isFittedForFunction($functionName)
    {
        return $functionName === 'alterColor';
    }
}
