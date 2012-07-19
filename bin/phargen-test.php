#!/usr/bin/php
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

namespace GDAPI;

include '../gdapi-php.phar';

$url        = 'https://api.cloud.secureserver.net/v1/schemas';
$url        = 'http://dbext01.dev.dbnow.ide/v1/schemas';
$access_key = 'C18BB8A947E63B25CB02';
$secret_key = 'KL6p1B2iVUNwjmFZ4Q2ofa1VA2tfKAHsGSjwHcGE';

$client = new \GDAPI\Client($url, $access_key, $secret_key);

var_dump($client->database->query());