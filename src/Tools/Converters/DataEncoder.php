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

namespace MSSLib\Tools\Converters;

/**
 * This class exists to hold all functions that can encode input data
 *
 * @author dobby007
 */
class DataEncoder {
    public static function b64EncodeFile($filepath) {
        $contents = file_get_contents($filepath);
        return base64_encode($contents);
    }
}
