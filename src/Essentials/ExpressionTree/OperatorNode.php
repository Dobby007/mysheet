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
class OperatorNode extends Node {
    /**
     * Gets operator's priority
     * @return integer
     */
    public function getOperatorPriority() {
        return $this->getOperator()->getPriority();
    }
    
    /**
     * Gets MathOperator object
     * @return \MSSLib\Essentials\Math\MathOperator
     */
    public function getOperator() {
        return $this->getValue();
    }
    
    /**
     * Sets MathOperator object
     * @return $this
     */
    public function setOperator(\MSSLib\Essentials\Math\MathOperator $operator) {
        $this->setValue($operator);
        return $this;
    }
}
