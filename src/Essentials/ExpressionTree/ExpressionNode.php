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

namespace MSSLib\Essentials\ExpressionTree;

use Tree\Node\Node;

/**
 * Description of OperatorNode
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class ExpressionNode extends Node {
    public function getValue() {
        return $this->getChildren();
    }
    
    public function setValue($value) {
        if (is_array($value)) {
            $this->setChildren($children);
        } else if ($value instanceof Node) {
            $this->setChildren($value->getChildren());
        }
        
        return $this;
    }
}
