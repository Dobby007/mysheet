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

namespace MSSLib\EmbeddedFunctions;

use MSSLib\EmbeddedClasses\ColorClass;
use MSSLib\Essentials\FunctionModule;
use MSSLib\EmbeddedClasses\MetricClass;
use MSSLib\Essentials\MssClass;

/**
 * ColorModule provides a set of functions to work with 'Color' MssClass
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class ColorModule extends FunctionModule
{
    public function alterColor(ColorClass $color, array $modifiers) {
        $color = clone $color;
        foreach ($modifiers as $modifier) {
            $value = $modifier['value'];
            $metric = $value['metric'];
            $isPercentage = $value['unit'] === '%';
            if ($isPercentage) {
                $metric = $metric / 100;
            }
            if ($value['explicitSign'] === true) {
                $color->addDeltaToColorChannel($modifier['name'], $metric, $isPercentage);
            } else {
                $color->setColorChannel($modifier['name'], $metric, $isPercentage);
            }
        }
        return $color;
    }
    
}
