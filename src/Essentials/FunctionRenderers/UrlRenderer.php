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
use MSSLib\Structure\Document;
use MSSLib\Tools\FileInfo;
use MSSLib\Helpers\MssClassHelper;
use MSSLib\EmbeddedClasses\DataUrlClass;
use MSSLib\Essentials\MssClass;

/**
 * Description of UrlFunctionRenderer
 *
 * @author dobby007
 */
class UrlRenderer implements IFunctionRenderer
{
    use \MSSLib\Traits\RootClassTrait;

    /**
     * @inheritdoc
     */
    public function parseArguments(FunctionClass $function, array $arguments) {
        $result = false;
        if (count($arguments) >= 1) {
            $firstArg = $arguments[0];
            if (!($firstArg instanceof MssClass)) {
                $result = DataUrlClass::parse($firstArg);
            }
            // parse with other registered classes
            if (!$result) {
                $result = MssClassHelper::parseMssClass($firstArg, array('sequence'), true);
            }
            if ($result instanceof MssClass) {
                return [$result];
            }
        }
        return [];
    }

    /**
     * @inheritdoc
     */
    public function renderArguments(FunctionClass $function, VariableScope $vars) {
        $fileUrl = $function->getArgument(0);
        if ($fileUrl === null) {
            return false;
        }
        $fileUrl = $fileUrl->getValue($vars);
        if ($fileUrl instanceof \MSSLib\Essentials\MssClassInterfaces\IGenericString) {
            $fileUrlStr = $fileUrl->getNonQuotedString();
            $prefix = $this->getSetting('urlFunction.autoPrefix', false);
            if (!filter_var($fileUrlStr, FILTER_VALIDATE_URL) && $this->getSetting('dataUrl.autoConvert')) {
                $fileUrlStr =  $prefix ? $prefix . $fileUrlStr : $fileUrlStr;
                $fullLocalPath = Document::makeRelativeFilePath(self::msInstance()->getActiveDocument(), $fileUrlStr);
                $fileInfo = new FileInfo($fullLocalPath);
                if ($fileInfo->fileExists && $fileInfo->fileSize / 1024 <= $this->getSetting('dataUrl.sizeLimit', 0)) {
                    return [DataUrlClass::fromFile($fullLocalPath, $fileInfo->mimeType)];
                }

            }
        }
        return [$fileUrl->toRealCss($vars)];
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
    function prepareArguments(FunctionClass $function, array $arguments)
    {
        return $arguments;
    }

    /**
     * @inheritdoc
     */
    function isFittedForFunction($functionName)
    {
        return $functionName === 'url';
    }


}
