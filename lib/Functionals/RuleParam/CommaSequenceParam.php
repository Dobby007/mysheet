<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MySheet\Functionals\RuleParam;

use MySheet\Essentials\RuleParam;

/**
 * Description of MetricParam
 *
 * @author dobby007
 */
class CommaSequenceParam extends RuleParam {
    protected $list;
    
    public function __construct(array $list) {
        $this->setList($list);
    }

    public function getList() {
        return $this->list;
    }

    public function setList(array $list) {
        $this->list = $list;
    }
    
    public function implodeList() {
        return implode(', ', $this->list);
    }
    
    public function toRealCss() {
        return $this->implodeList();
    }
    
    public function __toString() {
        return $this->toRealCss();
    }
    
    protected static function splitCommaList(&$string) {
        $i = 0;
        $offset = 0;
        $len = strlen($string);
        $quoteMet = false;
        $commaMet = false;
        $commaList = [];
        
        while ($i < $len) {
            if ($string[$i] === '(') {
                return false;
            } else if ( $string[$i] == '"' && ($i === 0 || ($i > 0 && $string[$i - 1] !== '\\')) ) {
                $quoteMet = !$quoteMet;
            } else if ($quoteMet === false && $string[$i] === ',') {
                $commaList[] = trim(substr($string, $offset, $i - $offset));
                $offset = ++$i;
                $commaMet = true;
            } else if ($quoteMet === false && $commaMet === false && (ctype_space($string[$i]) || $i === $len - 1)) {
                $commaList[] = trim(substr($string, $offset, $i - $offset + 1));
                $i++;
                break;
            } else {
                $commaMet = false;
            }
            $i++;
        }
        
        if (count($commaList) > 1) {
            $string = substr($string, $i);
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
