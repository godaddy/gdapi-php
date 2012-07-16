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

# From http://us2.php.net/manual/en/language.exceptions.php#91159
# ask at nilpo dot com 27-May-2009 07:19

interface ExceptionInterface
{
  /* Protected methods inherited from Exception class */
  public function getMessage();                 // Exception message 
  public function getCode();                    // User-defined Exception code
  public function getFile();                    // Source filename
  public function getLine();                    // Source line
  public function getTrace();                   // An array of the backtrace()
  public function getTraceAsString();           // Formated string of trace
  
  /* Overrideable methods inherited from Exception class */
  public function __toString();                 // formated string for display
  public function __construct($message = null, $code = 0);
}

abstract class CustomException extends \Exception implements ExceptionInterface
{
  protected $message = 'Unknown exception';     // Exception message
  private   $string;                            // Unknown
  protected $code = 0;                          // User-defined exception code
  protected $file;                              // Source filename of exception
  protected $line;                              // Source line of exception
  private   $trace;                             // Unknown

  public function __construct($message = null, $code = 0)
  {
    parent::__construct($message, $code);
  }
  
  public function __toString()
  {
    return get_class($this) . " '{$this->message}' in {$this->file}({$this->line})\n"
                              . "{$this->getTraceAsString()}";
  }
}

?>
