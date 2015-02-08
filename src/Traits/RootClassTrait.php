<?php

/*
 *  Copyright 2014 Alexander Gilevich (alegil91@gmail.com)
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at 
 * 
 *      http://www.apache.org/licenses/LICENSE-2.0
 */

namespace MSSLib\Traits;

use MSSLib\MySheet;
use MSSLib\Essentials\IMSSettings;

/**
 * Description of RootClassTrait
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
trait RootClassTrait {
    
    /**
     * @return MySheet Instance of MySheet class
     */
    public static function getRootObj() {
        return MySheet::Instance();
    }
    
    /**
     * @return IMSSettings
     */
    public function getSettings() {
        return self::getRootObj()->getSettings();
    }
    
    /**
     * @return mixed
     */
    public function getSetting($name, $default = null) {
        return self::getRootObj()->getSettings()->get($name, $default);
    }
    
}
