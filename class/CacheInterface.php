<?php
namespace GDAPI;

interface CacheInterface
{
  /**
   * Initialize the cache abstraction layer
   *
   * @param string $prefix The string that all keys will be prefixed
   *                       with in the cache
   * @param string $ttl    The time that the cache should live for
   *                       in seconds
   */
  public static function init($prefix = '', $default_ttl = 600);


  /**
   * Cache value getter
   *
   * @param string $key The key to fetch from the cache
   * @param boolean $status Passed-by-refence variable that contains the
   *               the call's status
   * @return mixed Return the cached value.  Note that you must check the
   *               value of $status to determine is the call was a success
   */
  public static function get($key, &$status = false);


  /**
   * Cache value setter
   *
   * @param string $key The key to identify the row in the cache
   * @param mixed $value The value to set in the cache
   * @param integer $ttl If ttl is set, the default ttl set in init
   *               is overridden with this new value.  If ttl is 0,
   *               the cache is persistent
   * @return boolean
   */
  public static function set($key, $val, $ttl = null);


  /**
   * Cache key deleter
   *
   * @param string $key The key to identify the row in the cache that
   *               will be deleted
   * @return boolean
   */
  public static function delete($key);



  /**
   * Cache clear.  Clears all cache keys.
   *
   * @param string $type Some caching layers group their cached variables.
   *                You probably shouldn't change this unless you know
   *                the underlying caching mechanism.
   * @return boolean
   */
  public static function clear($type = null);
}

?>
