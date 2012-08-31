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

class CurlRequest implements RequestInterface
{
  /*
   * cURL handle.
   */
  protected $curl;

  /*
   * Metadata about the last request and response
   */
  protected $last;


  public function __construct(&$client, $base_url)
  {
    $this->client = $client;
    $this->curl = curl_init();
    $this->base_url = $base_url;
    $this->applyOptions();
  }

  protected function applyOptions()
  {
    $o = $this->client->getOptions();

    $curl_opt = array(
      CURLOPT_USERAGENT       => static::getUserAgent(),
      CURLOPT_SSL_VERIFYPEER  => ($o['verify_ssl']       !== false ),
      CURLOPT_SSL_VERIFYHOST  => ($o['verify_ssl']       !== false ),
      CURLOPT_FOLLOWLOCATION  => ($o['follow_redirects'] !== false ),
      CURLOPT_MAXREDIRS       =>  $o['max_redirects'],
    );

    if ( $o['ca_cert'] )
    {
      $curl_opt[CURLOPT_CAINFO] = $o['ca_cert'];
    }

    if ( $o['ca_path'] )
    {
      $curl_opt[CURLOPT_CAPATH] = $o['ca_path'];
    }

    if ( static::supportsMSTimeouts() )
    {
      $curl_opt[CURLOPT_TIMEOUT_MS]         = ceil($o['response_timeout']*1000);
      $curl_opt[CURLOPT_CONNECTTIMEOUT_MS]  = ceil($o['connect_timeout']*1000);
    }
    else
    {
      $curl_opt[CURLOPT_TIMEOUT]         = ceil($o['response_timeout']);
      $curl_opt[CURLOPT_CONNECTTIMEOUT]  = ceil($o['connect_timeout']);
    }

    if ( $o['compress'] !== false )
    {
      // Empty string sends all supported encodings
      $curl_opt[CURLOPT_ENCODING] = '';
    }

    if ( $o['interface'] )
    {
      $curl_opt[CURLOPT_INTERFACE] = $o['interface'];
    }

    if ( $o['client_cert'] )
    {
      $curl_opt[CURLOPT_SSLCERT] = $o['client_cert'];
      
      if ( $o['client_cert_key'] )
      {
        $curl_opt[CURLOPT_SSLKEY] = $o['client_cert_key'];
      }

      if ( $o['client_cert_pass'] )
      {
        $curl_opt[CURLOPT_SSLKEYPASSWD] = $o['client_cert_pass'];
      }
    }

    curl_setopt_array($this->curl, $curl_opt);
  }

  static function getUserAgent()
  {
    $ua = "GDAPI/". Client::VERSION .
                  " PHP/". phpversion() .
                 " cURL/". static::getPHPVersionID();
    return $ua;
  }

  static function getCurlVersionString()
  {
    $curl_version = curl_version();
    return $curl_version['version'];
  }

  static function getCurlVersionID()
  {
    $curl_version = curl_version();
    return $curl_version['version_number'];
  }

  static function getPHPVersionID()
  {
    if ( defined('PHP_VERSION_ID') )
    {
      $php_version_id = PHP_VERSION_ID;
    }
    else
    {
      $version = explode('.', PHP_VERSION);
      $php_version_id = $version[0] * 10000 + $version[1] * 100 + $version[2];
    }
    
    return $php_version_id;
  }

  static function supportsMSTimeouts()
  {
    $php = static::getPHPVersionID();
    $curl = static::getCurlVersionID();
    
    // Supported in PHP 5.2.3+ and cURL 7.16.2+
    if ( $php >= 50203 && $curl >= 0x71002 )
    {
      return true;
    }

    return false;
  }

  public function setAuth($access_key, $secret_key)
  {
    curl_setopt_array($this->curl, array(
      CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
      CURLOPT_USERPWD  => $access_key .":". $secret_key,
    ));
  }

  public function request($method, $path, $qs=array(), $body=null, $content_type=false)
  {
    $o = &$this->client->getOptions();
    $method = strtoupper($method);

    curl_setopt($this->curl, CURLOPT_HEADER, $o['stream_output'] === false );
    curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, $o['stream_output'] === false );

    $headers = $o['headers'];
    if ( !$headers )
    {
      $headers = array();
    }

    if ( $o['keep_alive'] )
    {
      $headers['Connection'] = 'Keep-Alive';
      $headers['Keep-Alive'] = $o['keep_alive'];
    }

    if ( stripos($path,'http') === 0 )
    {
      $url = $path;
    }
    else
    {
      $url = $this->base_url . $path;
    }

    if ( isset($qs) && count($qs) )
    {
      $url .= (( strpos($url,'?') === false ) ? '?' : '&') . http_build_query($qs);
    }
    curl_setopt($this->curl, CURLOPT_URL, $url);

    if ( $body !== null )
    {
      // JSON encode objects that are passed in
      if ( $content_type && stripos($content_type,'json') !== false && !is_string($body) )
      {
        $body = json_encode($body);
      }

      if ( $content_type )
      {
        $headers['Content-Type'] = $content_type;
      }

      if ( is_array($body) )
      {
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $body);
      }
      else
      {
        $headers['Content-Length'] = strlen($body);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $body);
      }
    }
    else if ( $method == 'POST' )
    {
      $headers['Content-Length'] = 0;

      // Must set this to null to clear out the body from a previous request
      curl_setopt($this->curl, CURLOPT_POSTFIELDS, null);
    }

    curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $method);

    $flat_headers = array();
    foreach ( $headers as $k => $v )
    {
      unset($headers[$k]);
      $k = strtolower($k);
      $headers[$k] = $v;
      $flat_headers[] = "$k: $v";
    }
    curl_setopt($this->curl, CURLOPT_HTTPHEADER, $flat_headers);

    $this->last = array(
      'method'          => $method,
      'url'             => $url,
      'request_headers' => $headers,
    );

    $response = curl_exec(   $this->curl);
    $info     = curl_getinfo($this->curl);
    $errno    = curl_errno(  $this->curl);
    $error    = curl_error(  $this->curl);

    $this->last['status'] = $response_code = $info['http_code'];
    $this->last['errno'] = $errno;
    $this->last['error'] = $error;
    $this->last['info'] = $info;
    $this->last['response'] = $response;

    if ( $errno )
    {
      return $this->client->error($error,'request',$this->last);
    }
    else if ( $o['stream_output'] !== false )
    {
      return true;
    }
    else
    {
      $body = $this->parseResponse($response,$info);

      if ( $response_code >= 200 && $response_code <= 299 )
      {
        return $body;
      }
      else if ( $body )
      {
        $message = '';
        if ( $body->metaIsSet('message') )
        {
          $message = $body->getMessage();
        }

        return $this->client->error($message, $response_code, $body);
      }
      else
      {
        return $this->client->error('', $response_code, $response);
      }
    }
  }

  protected function parseResponse($res,$meta)
  {
    $o = $this->client->getOptions();

    $raw_headers  = substr($res,0,$meta['header_size']);
    $raw_body     = substr($res,$meta['header_size']);

    $headers = array();
    $lines = explode("\r\n",$raw_headers);

    if ( preg_match("/^HTTP\/([0-9.]+)\s+(\d+)\s+(.*)$/", $lines[0], $match) )
    {
      $this->last['http_version'] = $match[1];
      $this->last['status_msg'] = $match[3];
    }
    else
    {
      return $this->client->error('Failed parsing HTTP response', 'parse');
    }

    for ( $i = 1, $len = count($lines) ; $i < $len ; $i++ )
    {
      $line = trim($lines[$i]);
      if ( !$line )
      {
        continue;
      }

      $pos = strpos($line,":");
      $k = strtolower(substr($line,0,$pos));
      $v = substr($line,$pos+1);
      $headers[$k] = $v;
    }
    $this->last['response_headers'] = $headers;

    if ( isset($headers['content-type']) && strpos($headers['content-type'],'json') !== false )
    {
      if ( PHP_VERSION_ID >= 50400 )
      {
        $json = json_decode($raw_body, false, $o['json_depth_limit'], JSON_BIGINT_AS_STRING);
      }
      else
      {
        $json = json_decode($raw_body, false, $o['json_depth_limit']);
      }

      if ( $err = json_last_error() == JSON_ERROR_NONE )
      {
        $json = $this->client->classify($json);
        return $json;
      }
      else
      {
        return $this->client->error("JSON Decode error: $err", 'parse');
      }
    }
  }

  public function getMeta()
  {
    return $this->last;
  }
}
