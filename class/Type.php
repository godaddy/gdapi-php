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

class Type
{
  private $clientId;

  public function __construct($clientId,$schema)
  {
    $this->clientId = $clientId;
    $this->schema = $schema;
  }
  
  public function getById($id)
  {
    $url = $this->getUrl($id);
    $client = $this->getClient();
    return $client->request('GET', $url);
  }

  public function query($filters=array(), $sort=array(), $pagination=array(), $include=array())
  {
    $options = array(
      'filters'     => $filters,
      'sort'        => $sort,
      'pagination'  => $pagination,
      'include'     => $include,
    );

    $url = static::listHref($this->getUrl(), $options);
    $client = $this->getClient();
    return $client->request('GET', $url);
  }

  public function create($obj)
  {
    $data = ( $obj instanceof Resource ? $obj->getMeta() : $obj );
    $url = $this->getUrl();
    $client = $this->getClient();
    return $client->request('POST', $url, array(), $data, Client::MIME_TYPE_JSON);
  }

  public function remove($id_or_obj)
  {
    $id = ( $id_or_obj instanceof Resource ? $id_or_obj->getId() : $id_or_obj );
    $url = $this->getUrl($id);
    $client = $this->getClient();
    return $client->request('DELETE', $url);
  }

  public function schema()
  {
    return $this->schema;
  }

  public function resourceField($name)
  {
    if ( $this->schema->metaIsSet('resourceFields') )
      $fields =  $this->schema->getResourceFields();
    else
      $fields =  $this->schema->getFields();

    if ( isset($fields->{$name}) )
    {
      return $fields->{$name};
    }

    return null;
  }

  public function collectionField($name)
  {
    $fields =  $this->schema->getCollectionFields();
    if ( isset($fields->{$name}) )
    {
      return $fields->{$name};
    }

    return null;
  }

  protected function getUrl($id=false)
  {
    return $this->schema->getLink('collection') . ($id === false ? '' : '/'.urlencode($id) );
  }

  public static function listHref($url, $opt)
  {
    $opt = static::arrayify($opt);
    $qs = parse_url($url,PHP_URL_QUERY);

    # Filters
    if ( isset($opt['filters']) && count($opt['filters']) )
    {
      // 'filters' is a hash of field names => filter or array(filters)
      // Each filter value can be:
      // - A simple literal like 'blah', ("name equals blah")
      // - A hash with modifier and/or value: array('modifier' => 'ne', value => 'blah') ("name is not equal to blah")
      // - An array of one or more of the above: array('blah', array('modifier' => 'notnull') ("name is equal to blah AND name is not null")

      // Loop over the hash of each field name
      foreach ( $opt['filters'] as $fieldName => $list )
      {
        // Turn whatever the input was into an aray of individual filters to check
        if ( !is_array($list) )
        {
          // Simple value
          $list = array($list);
        }
        else if ( isset($list['value']) || isset($list['modifier']) )
        {
          // It's an "array", but really a hash like array('modifier' => 'blah', 'value' => blah')
          $list = array($list);
        }
        else
        {
          // Already an array of individual filters, do nothing
        }

        // Loop over each individual filter for this field
        foreach ( $list as $filter )
        {
          // This is a filter like  array('modifier' => 'blah', 'value' => blah')
          if ( is_array($filter) && ( isset($filter['value']) || isset($filter['modifier']) ) )
          {
            $name = $fieldName;

            if ( isset($filter['modifier']) && $filter['modifier'] != '' && $filter['modifier'] != 'eq' )
              $name .= '_' . $filter['modifier'];

            $value = null;
            if ( isset($filter['value']) )
            {
              $value = $filter['value'];
            }
          }
          else
          {
            // This is a simple literal name=value literal
            $name = $fieldName;
            $value = $filter;
          }
          
          $qs .= '&' . urlencode($name);

          // Only add value if it's meaningful
          // (Note: A filter with value => null is invalid, use array('modifier' => 'null') to say that a field is null)
          if ( $value !== null )
            $qs .= '='. urlencode($value);
        }
      }
    }

    # Sorting
    if ( isset($opt['sort']) && count($opt['sort']) )
    {
      if ( is_array($opt['sort']) )
      {
        $qs .= '&sort=' . urlencode($opt['sort']['name']);
        if ( isset($opt['sort']['order']) && strtolower($opt['sort']['order']) == 'desc' )
        {
          $qs .= '&order=desc';
        }
      }
    }

    # Pagination
    if ( isset($opt['pagination']) && count($opt['pagination']) )
    {
      $qs .= '&limit=' . intval($opt['pagination']['limit']);

      if ( isset($opt['pagination']['marker']) )
      {
        $qs .= '&marker=' . urlencode($opt['pagination']['marker']);
      }
    }

    # Include
    if ( isset($opt['include']) && count($opt['include']) )
    {
      foreach ( $opt['include'] as $link )
      {
        $qs .= '&include=' . urlencode($link);
      }
    }

    $base_url = preg_replace("/\?.*/","",$url);
    $out = $base_url;
    if ( $qs )
    {
      // If the initial URL query string was empty, there will be an extra & at the beginning
      $out .= '?' . preg_replace("/^&/","",$qs);
    }
    
    return $out;
  }
  
  static function arrayify($obj)
  {
    if ( is_object($obj) )
      $ary = get_object_vars($obj);
    else
      $ary = $obj;

    foreach ( $ary as $k => $v )
    {
      if ( is_array($v) || is_object($v) )
      {
        $v = static::arrayify($v);
        $ary[$k] = $v;
      }
    }

    return $ary;
  }

  protected function &getClient()
  {
    return Client::get($this->clientId);
  }
}

?>
