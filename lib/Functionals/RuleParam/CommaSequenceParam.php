<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Functionals\RuleParam;

use MySheet\Essentials\RuleParam;
use MySheet\Essentials\FuncListManager;
use MySheet\Helpers\ArrayHelper;
use MySheet\Helpers\StringHelper;
use MySheet\Essentials\VariableScope;
use MySheet\Traits\RuleParamListTrait;
use MySheet\Essentials\DelimitedString;

/**
 * Description of MetricParam
 *
 * @author dobby007
 */
class CommaSequenceParam extends RuleParam {
    use RuleParamListTrait;
    
    protected $delimitedString;
    
    public function __construct(array $list) {
        $this->setItemList($list);
    }

    /**
     * 
     * @return DelimitedString
     */
    public function getDelimitedString() {
        return $this->delimitedString;
    }

    public function setItemList($list) {
        //call protected function from outside
        $this->delimitedString = new DelimitedString($list, function ($item) {
            return $this->parseNestedParam($item);
        });
    }
    
    public function toRealCss(VariableScope $vars = null) {
        return ArrayHelper::implode_objects(', ', $this->getDelimitedString()->getList(), 'toRealCss', $vars);
    }
    
    public function __toString() {
        return $this->toRealCss();
    }
    
    protected static function splitCommaList(&$string) {
        $stringCopy = $string;
        echo "\n";
        $commaList = StringHelper::parseSplittedString($stringCopy, ',');
        
//        var_dump('Source String: ', $string, 'Comma List: ', $commaList);
        
        if (count($commaList) > 1) {
            $string = $stringCopy;
            return $commaList;
        }
        
        
        
        return false;
    }
    
    public static function parse(&$string) {
        $commaList = self::splitCommaList($string);
        if (is_array($commaList)) {
            return new self($commaList);
        }
        return false;
    }
}
