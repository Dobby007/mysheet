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

namespace MSSLib\Plugins\Mixin\EmbeddedMixins;

use MSSLib\Plugins\Mixin\MixinSet;
use MSSLib\Plugins\Mixin\Mixin;
use MSSLib\Structure\Declaration;
use MSSLib\Structure\RuleValue;
use MSSLib\Etc\Constants;
/**
 * BasicModule provides a basic set of mixins. That't it!
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class BasicSet extends MixinSet
{
    protected static $_mixins = [
        'border-radius',
        'transform',
        'filter-grayscale',
    ];
    
    public function border_radius() {
        $genericRuleValue = new RuleValue('$arguments');
        return Mixin::builder()->setName('border-radius')
                               ->addDeclarations(Declaration::createPrefixedDeclarations('border-radius', $genericRuleValue, Constants::BROWSER_MOZILLA | Constants::BROWSER_WEBKIT))
                               ->getResult();
        
    }
    
    public function transform() {
        $genericRuleValue = new RuleValue('$arguments');
        return Mixin::builder()->setName('transform')
                               ->addDeclarations(Declaration::createPrefixedDeclarations('transform', $genericRuleValue))
                               ->getResult();
        
    }
    
    public function filter_grayscale() {
        $genericRuleValue = new RuleValue('grayscale($percent)');
        return Mixin::builder()->setName('filter-grayscale')
                               ->addLocalParameter('percent')
                               ->addDeclarations(Declaration::createPrefixedDeclarations('filter', $genericRuleValue, Constants::BROWSER_WEBKIT))
                               ->getResult();
    }
    
}
