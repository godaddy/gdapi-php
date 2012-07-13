<?php
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
    return $client->request('POST', $url, array(), $data, true);
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
