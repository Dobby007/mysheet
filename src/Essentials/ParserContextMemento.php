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

namespace MSSLib\Essentials;

/**
 * Memento of ParserContext state
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class ParserContextMemento
{
    public $closure;
    public $lineIndex;
    public $upperClosureLimit;
}
