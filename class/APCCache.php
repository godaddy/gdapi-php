<?php
namespace GDAPI;

class APCCache extends AbstractCache
{
  public static function get($key, &$status = false)
  {
    return apc_fetch(static::$prefix . $key, $status);
  }

  public static function set($key, $val, $ttl = null)
  {
    return apc_store(self::$prefix . $key, $val, $ttl);
  }

  public static function remove($key)
  {
    return apc_delete(self::$prefix . $key);
  }

  public static function clear($type = null)
  {
    return apc_clear_cache('user');
  }
}

?>
