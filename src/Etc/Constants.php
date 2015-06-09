<?php

/*
 * Copyright 2015 dobby007.
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

namespace MSSLib\Etc;

/**
 * Description of Constants
 *
 * @author dobby007
 */
abstract class Constants {
    const BROWSER_MOZILLA = 0b0001;
    const BROWSER_IE = 0b0010;
    const BROWSER_OPERA = 0b0100;
    const BROWSER_WEBKIT = 0b1000;
    const BROWSER_BLINK = 0b10000;
    const BROWSER_ALL = 0b11111;


    const CHANNEL_RED = 'red';
    const CHANNEL_GREEN = 'green';
    const CHANNEL_BLUE = 'blue';
    const CHANNEL_HUE = 'hue';
    const CHANNEL_SATURATION = 'saturation';
    const CHANNEL_LIGHTNESS = 'lightness';
    const CHANNEL_ALPHA = 'alpha';

}
