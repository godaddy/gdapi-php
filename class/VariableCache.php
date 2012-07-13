<?php
namespace GDAPI;

class VariableCache extends AbstractCache
{
  private static $cache = array();

  public static function get($key, &$status = false)
  {
    $status = true;
    if ( isset(self::$cache[self::$prefix . $key]) )
    { 
      return self::$cache[self::$prefix . $key];
    }

    $status = false;
    return null;
  }

  public static function set($key, $val, $ttl = null)
  {
    self::$cache[self::$prefix . $key] = $val;
    return true;
  }

  public static function remove($key)
  {
    if (!isset(self::$cache[self::$prefix . $key]))
    {  
      return false;
    }  

    unset(self::$cache[self::$prefix . $key]);
    return true;
  }

  public static function clear()
  {
    self::$cache = array();
    return true;
  }
}

?>
