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

namespace MSSLib\Plugins;
use MSSLib\Traits\RootClassTrait;
use MSSLib\Traits\MagicPropsTrait;
/**
 * Description of PluginBase
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
abstract class PluginBase {
    use RootClassTrait,
        MagicPropsTrait;
    
    public function __construct() { }

    
    abstract public function init();
}
