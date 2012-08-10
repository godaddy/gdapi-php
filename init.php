<?php
/*
 * Copyright (c) 2012 Go Daddy Operating Company, LLC
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 */

 $dir = dirname(__FILE__);

require_once($dir . '/class/CustomException.php');
require_once($dir . '/class/APIException.php');

require_once($dir . '/class/RequestInterface.php');
require_once($dir . '/class/CurlRequest.php');

require_once($dir . '/class/CacheInterface.php');
require_once($dir . '/class/AbstractCache.php');
require_once($dir . '/class/VariableCache.php');
require_once($dir . '/class/APCCache.php');

require_once($dir . '/class/Resource.php');
require_once($dir . '/class/Error.php');
require_once($dir . '/class/Collection.php');
require_once($dir . '/class/Type.php');

require_once($dir . '/class/Client.php');

?>
