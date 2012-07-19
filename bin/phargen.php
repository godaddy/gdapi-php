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

$classPath = '../class/';
$initPath = '../';
$pharName = '../gdapi-php.phar';

// create the phar archive
$oPhar = new \Phar('./' . $pharName);

// create a directory iterator for the class dir
$oDir = new \RecursiveIteratorIterator(
  new \RecursiveDirectoryIterator($classPath),
  \RecursiveIteratorIterator::SELF_FIRST
);

// add the classes
foreach($oDir as $file) {

	// exclude . & ..
	if($file->isFile()) {

		// add to the archive, not stripping whitespace or anything for now
		$oPhar->addFromString($file->getFilename(), file_get_contents($file));

	}
}

// create the stub from the init file
$stub = str_replace(
  "('class/",
  "('phar://$pharName/",
  file_get_contents($initPath . '/init.php')
) . " __HALT_COMPILER(); ?>";

// set the stub, and all done!
$oPhar->setStub($stub);