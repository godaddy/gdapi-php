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

class Collection extends Resource implements \ArrayAccess, \Iterator, \Countable
{
  private $data = array();
  private $pos = 0;

  public function __construct($clientId, $body=false)
  {
    if ( $body && $body->data )
    {
      $this->data = $body->data;
      unset($body->data);
    }

    parent::__construct($clientId,$body);
  }

  protected function schemaField($name)
  {
    $type_name = $this->getType();
    $type = $this->getClient()->{$type_name};

    if ( !$type )
    {
      return null;
    }
    
    $field = $type->collectionField($name);
    return $field;
  }

  /* ArrayAccess */
  public function offsetExists($offset)
  {
    return isset($this->data[$offset]);
  }

  public function offsetGet($offset)
  {
    if ( isset($this->data[$offset]) )
      return $this->data[$offset];

    return null;
  }

  public function offsetSet($offset, $value)
  {
    if ( is_null($offset) )
    {
      $this->data[] = $value;
    }
    else
    {
      $this->data[$offset] = $value;
    }
  }

  public function offsetUnset($offset)
  {
    unset($this->data[$offset]);
  }
  /* End: ArrayAccess */

  /* Iterator */
  public function current()
  {
    return $this->data[$this->pos];
  }

  public function key()
  {
    return $this->pos;
  }

  public function next()
  {
    $this->pos++;
  }

  public function rewind()
  {
    $this->pos = 0;
  }

  public function valid()
  {
    return isset($this->data[$this->pos]);
  }
  /* End: Iterator */

  /* Countable */
  public function count()
  {
    return count($this->data);
  }
  /* End: Countable */

  /* Operations */
  public function create($obj)
  {
    $data = ( $obj instanceof Resource ? $obj->getMeta() : $obj );
    $url = $this->getLink('self');
    $client = $this->getClient();
    return $client->request('POST', $url, array(), $data, Client::MIME_TYPE_JSON);
  }

  public function remove($id_or_obj)
  {
    $id = ( $id_or_obj instanceof Resource ? $id_or_obj->getId() : $id_or_obj );
    $url = $this->getLink('self').'/'. urlencode($id);
    $client = $this->getClient();
    return $client->request('DELETE', $url);
  }
  /* End: Operations */
}
