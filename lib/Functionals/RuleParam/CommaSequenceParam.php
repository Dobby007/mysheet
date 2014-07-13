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

/**
 * Description of MetricParam
 *
 * @author dobby007
 */
class CommaSequenceParam extends RuleParam {
    use RuleParamListTrait;
    
    protected $list;
    
    public function __construct(array $list) {
        $this->setList($list);
    }

    public function getList() {
        return $this->list;
    }

    public function setList(array $list) {
        foreach ($list as &$listItem) {
            $listItem = $this->parseListItem($listItem);
        }
        $this->list = $list;
    }
    
    protected function parseListItem($item) {
        $result = null;
        $this->getRoot()->getListManager()->iterateList('RuleParam', function ($paramClass) use ($item, &$result) {
            if ($paramClass == __CLASS__) {
                return;
            }
            
            $res = RuleParam::tryParse($paramClass, $item);
            if ($res instanceof RuleParam) {
                $result = $res;
                FuncListManager::stopIteration();
            }
        });
        
        if (!$result) {
            $item = ltrim($item);
            $result = OtherParam::parse($item);
        }
        
        return $result;
    }
    
    public function toRealCss(VariableScope $vars = null) {
        return ArrayHelper::implode_objects(', ', $this->list, 'toRealCss', $vars);
    }
    
    public function __toString() {
        return $this->toRealCss();
    }
    
    protected static function splitCommaList(&$string) {
        $stringCopy = $string;
        echo "\n";
        $commaList = StringHelper::parseSplittedString($stringCopy, ',', true);
        
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
