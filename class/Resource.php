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

class Resource
{
  private $clientId;
  private $meta = array();
  private $links = array();
  private $actions = array();

  public function __construct($clientId,$body=false)
  {
    $this->clientId = $clientId;

    if ( $body )
    {
      foreach ( $body as $key => $value )
      {
        if ( $key == 'links' )
        {
          $this->links = $value;
        }
        else if  ( $key == 'actions' )
        {
          $this->actions = $value;
        }
        else if ( $key == 'body' )
        {
          // This should only be in a collection...
        }
        else
        {
          $this->meta[$key] = $value;
        }
      }
    }
  }

  public function __call($callName, $args)
  {
    // Links
    if ( strpos($callName, 'fetch') === 0 )
    {
      $name = lcfirst(substr($callName,5));
      return $this->doFetch($name,$args);
    }
    // Field Getter
    elseif ( strpos($callName, 'get') === 0 )
    {
      $name = lcfirst(substr($callName,3));
      if ( array_key_exists($name, $this->meta) )
      {
        // Return timestamps in seconds, not milliseconds.
        if ( strtoupper(substr($name, -2)) == 'TS' )
        {
          $type = $this->getType();
          $shortname = substr($name, 0, -2);
          $field = $this->getClient()->{$type}->field($shortname);
          if ( $field && isset($field->type) && $field->type == 'date' )
          {
            return $this->meta[$name]/1000;
          }
        }

        return $this->meta[$name];
      }
      else
      {
        trigger_error("Attempted to access unknown property '$name' on Resource object: " . print_r($this->meta,true), E_USER_WARNING);
        return null;
      }
    }
    // Field Setter
    elseif ( strpos($callName, 'set') === 0 )
    {
      $name = lcfirst(substr($callName,3));
      $this->meta[$name] = $args[0];
      return true;
    }
    else if ( strpos($callName, 'do') === 0 )
    {
      $name = lcfirst(substr($callName,2));
      return $this->doAction($name,$args);
    }
  }

  public function metaIsSet($name)
  {
    return isset($this->meta[$name]);
  }

/*
  public function __get($name)
  {
    if ( isset($this->meta[$name]) )
    {
      return $this->meta[$name];
    }
  }

  public function __set($name,$value)
  {
    if ( isset($this->meta[$name]) )
    {
      return $this->meta[$name];
    }
  }

  public function __isset($name)
  {
    return isset($this->meta[$name]);
  }

  public function __unset($name)
  {
    unset($this->meta[$name]);
  }
*/

  protected function &getClient()
  {
    return Client::get($this->clientId);
  }

  protected function doFetch($name,$args)
  {
    $opt = array();
    if ( isset($args,$args[0]) )
      $opt['filters'] = $args[0];

    if ( isset($args,$args[1]) )
      $opt['sort'] = $args[1];

    if ( isset($args,$args[2]) )
      $opt['pagination'] = $args[2];

    if ( isset($args,$args[3]) )
      $opt['include'] = $args[3];

    $link = Type::listHref($this->getLink($name), $opt);

    if ( !$link )
    {
      return null;
    }

    $client = $this->getClient();
    return $client->request('GET', $link);
  }

  public function getLinks()
  {
    return $this->links;
  }

  public function getLink($name)
  {
    if ( isset($this->links->{$name}) )
    {
      return $this->links->{$name};
    }

    return null;
  }

  protected function doAction($name,$args)
  {
    $opt = array();
    if ( isset($args,$args[0]) )
    {
      $opt = $args[0];
    }

    if (!isset($this->actions->{$name}) )
    {
      return null;
    }

    $link = $this->actions->{$name};

    if ( !$link )
    {
      return null;
    }

    $client = $this->getClient();
    return $client->request('POST', $link, array(), $opt, true);
  }

  public function getMeta()
  {
    return $this->meta;
  }

  public function save()
  {
    $link = $this->getLink('self');
    $client = $this->getClient();
    return $client->request('PUT', $link, array(), $this->meta, true);
  }

  public function remove()
  {
    $link = $this->getLink('self');
    $client = $this->getClient();
    return $client->request('DELETE', $link);
  }
}

?>
