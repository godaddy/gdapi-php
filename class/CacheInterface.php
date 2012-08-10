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
  public static function remove($key);



  /**
   * Cache clear.  Clears all cache keys.
   * @return boolean
   */
  public static function clear();
}

?>
