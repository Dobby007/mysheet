<?php

/*
 * Copyright 2014 dobby007 (Alexander Gilevich, alegil91@gmail.com).
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

namespace MSSLib\Helpers;

use MSSLib\Traits\RootClassTrait;
use MSSLib\Essentials\RuleFlag\RuleFlag;
use MSSLib\Essentials\RuleFlag\RuleFlagCreator;
use MSSLib\Helpers\StringHelper;

/**
 * Description of RuleFlagHelper
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class RuleFlagHelper
{
    use RootClassTrait;
    
    public static function parseRuleFlag(&$inputString) {
        // return input if it is already an instance of RuleFlag
        if ($inputString instanceof RuleFlag) {
            return $inputString;
        }
        $inputCopy = $inputString;
        $flagArray = StringHelper::parseRuleFlag($inputCopy);
        if (!$flagArray) {
            return false;
        }
        $inputString = ltrim($inputCopy);
        return self::msInstance()->getListManager()->getList('RuleFlag')->iterate(function(RuleFlagCreator $ruleFlagCreator) use ($flagArray) {
            $res = $ruleFlagCreator->createFromArray($flagArray);
            return $res instanceof RuleFlag ? $res : null;
        });
    }
}
