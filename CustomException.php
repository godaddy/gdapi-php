<?php
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
  protected $code    = 0;                       // User-defined exception code
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
