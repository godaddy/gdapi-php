<?php
namespace GDAPI;

abstract class AbstractCache implements CacheInterface
{
  /**
   * Prefix cache entries with this string to prevent key collisions in shared caches.
   *
   * @var string $prefix
   */
  protected static $prefix = '';

  /**
   * Default time-to-live for variables in the cache
   *
   * @var integer $ttl
   */
  protected static $ttl = 600;

  /**
   *
   */
  public static function init($prefix = '', $ttl = 600)
  {
    self::$prefix = $prefix;
    self::$ttl = $ttl;
  }

  /**
   * Change the prefix
   * 
   * @param string $prefix The new prefix
   */
  public static function setPrefix($prefix = '')
  {
    self::$prefix = $prefix;
  }


}

?>
