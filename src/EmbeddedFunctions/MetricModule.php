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

use MSSLib\Essentials\FunctionModule;
use MSSLib\EmbeddedClasses\MetricClass;

/**
 * MetricModule provides a set of function to work with 'Metric' MssClass
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class MetricModule extends FunctionModule
{
    public function abs($value) {
        $value = $value->getValue($this->getVars());
        return new MetricClass(abs($value->getMetric()), $value->getUnit()); 
    }
    
    public function negate($value) {
        $value = $value->getValue($this->getVars());
        return new MetricClass(-$value->getMetric(), $value->getUnit()); 
    }
    
    public function unitless($value) {
        $value = $value->getValue($this->getVars());
//        return null;
        return new MetricClass(-$value->getMetric(), null); 
    }
    
}
