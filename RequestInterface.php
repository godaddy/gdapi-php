<?php
namespace GDAPI;

interface RequestInterface
{
  public function __construct(&$client, $base_url, $options=array());
  public function setAuth($access_key, $secret_key);
  public function request($method, $path, $qs=array(), $body=null, $content_type=false);
  public function getMeta();
}
