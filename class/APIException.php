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

class APIException extends CustomException
{
  static $status_map = array();

  protected $response = array();

  public function __construct($message=null, $code=0, $response=null)
  {
    parent::__construct($message, $code);

    if ( $response )
    {
      if ( is_object($response) || is_array($response) )
      {
        $this->response = $response;
      }
      else if ( $response )
      {
        $this->response = json_decode($response,true);
      }
    }
  }

  public function __toString()
  {
    $msg = get_class($this) . " '{$this->message}' in {$this->file}({$this->line})\n";

    if ( $this->response && $this->response instanceof Error )
    {
      $res = $this->response;

      if ( $res->metaIsSet('status') )
      {
        $msg .= "HTTP Status: " . $res->getStatus() . "\n";
      }

      if ( $res->metaIsSet('code') )
      {
        $msg .= "Code: " . $res->getCode() . "\n";
      }

      if ( $res->metaIsSet('message') )
      {
        $msg .= "Message: " . $res->getMessage() . "\n";
      }

      if ( $res->metaIsSet('detail') )
      {
        $msg .= "Detail: " . $res->getDetail() . "\n";
      }
    }

    $msg .= "{$this->getTraceAsString()}";
    return $msg;
  }

  public function getResponse()
  {
    return $this->response;
  }
}

class ClientException         extends APIException {};
class SchemaException         extends APIException {};
class HTTPRequestException    extends APIException {};
class ParseException          extends APIException {};
class UnknownTypeException    extends APIException {};

class BadRequestException     extends APIException {};
class UnauthorizedException   extends APIException {};
class ForbiddenException      extends APIException {};
class NotFoundException       extends APIException {};
class MethodException         extends APIException {};
class NotAcceptableException  extends APIException {};
class ConflictException       extends APIException {};
class ServiceException        extends APIException {};
class UnavailableException    extends APIException {};

APIException::$status_map = array(
  'schema'  => '\GDAPI\SchemaException',
  'client'  => '\GDAPI\ClientException',
  'request' => '\GDAPI\HTTPRequestException',
  'parse'   => '\GDAPI\ParseException',
  'type'    => '\GDAPI\UnknownTypeException',

  400       => '\GDAPI\BadRequestException',
  401       => '\GDAPI\UnauthorizedException',
  403       => '\GDAPI\ForbiddenException',
  404       => '\GDAPI\NotFoundException',
  405       => '\GDAPI\MethodException',
  406       => '\GDAPI\NotAcceptableException',
  409       => '\GDAPI\ConflictException',
  500       => '\GDAPI\ServiceException',
  503       => '\GDAPI\UnavailableException'
);


?>
