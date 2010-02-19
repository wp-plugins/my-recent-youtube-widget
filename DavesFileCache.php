<?php

/**
 * Copyright (c) 2009 Dave Ross <dave@csixty4.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit
 * persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 *   The above copyright notice and this permission notice shall be included in all copies or substantial portions of the
 * Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR 
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR 
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 **/

if(!class_exists('DavesFileCache')) {
	
/**
 * Cheap implementation of a file cache, stolen from an old theme I wrote
 * @version 1.1
 */
class DavesFileCache
{
	var $CACHE_DIR = "cache";

	var $identifier = null;
	var $cacheDir = null;

	var $expiration = null;
	var $contents = null;

	function __construct($identifier, $cacheDir = NULL)
	{
		$this->identifier = $identifier;
		if(NULL == $cacheDir) {
			$cacheDir = dirname(__FILE__).'/'.$this->CACHE_DIR;
		}
		$this->cacheDir = $cacheDir;
	}

	/**
	 * Retrieve an item from the cache if available and not expired
	 *
	 * @param string $identifier
	 * @return unknown
	 * @static 
	 */
	function forIdentifier($identifier, $cacheDir = NULL)
	{
		$cache = new DavesFileCache($identifier, $cacheDir);
		$cacheFile = $cache->getFilePath();
		
		if(file_exists($cacheFile))
		{
			$cachedObj = unserialize(file_get_contents($cacheFile));
			if(!$cachedObj->isExpired()) return $cachedObj;
			unlink($cacheFile);
		}

		throw new Exception('invalid cache identifier');
	}

	/**
	 * @param $expiration integer seconds (defaults to 21600 = 6 hours)
	 */ 
	function store($contents, $expiration = 21600)
	{
		$this->expiration = time() + $expiration;
		$this->contents = $contents;

		file_put_contents($this->getFilePath(), serialize($this));
	}

	/**
	 * 
	 *
	 * @return unknown
	 */
	function get()
	{
		return $this->contents;
	}

	/**
	 * Check if the item is expired
	 *
	 * @return unknown
	 */
	function isExpired()
	{
		return (time() > $this->expiration);
	}

	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	function getFilePath()
	{
		return $this->getFileDir()."/".md5($this->identifier).".cache";
	}
	
	function getFileDir()
	{
		return $this->cacheDir;
	}
	
	/**
	 * Test if the cache dir exists and is writable
	 * 
	 * @static
	 * @return boolean
	 */
	function testCacheDir()
	{
		$instance = new DavesFileCache(uniqid());
		return is_writable($instance->getFileDir());
	}
}

}
?>