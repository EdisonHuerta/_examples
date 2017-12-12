<?php
/**
 *
 * @Singleton to create uri fragments
 *
 * @copyright Copyright (C) 2013 PHPRO.ORG. All rights reserved.
 *
 * @license new bsd http://www.opensource.org/licenses/bsd-license.php
 * @package Files
 * @Author Kevin Waterson
 *
 */

namespace skf;

class uri 
{
	/*
	 * @var array $fragments
	 */
	public $fragments = array();


	/**
	 *
	 * Constructor, Duh!
	 *
	 */
	public function __construct()
	{
		/*** put the string into array ***/
		$this->fragments = explode('/', $_SERVER['QUERY_STRING']);
	}

	/**
	 * @get uri fragment 
	 *
	 * @access public
	 * @param string $key:The uri key
	 * @return string on success
	 * @return bool false if key is not found
	 *
	 */
	public function fragment($key)
	{
		if(array_key_exists($key, $this->fragments))
		{
			return $this->fragments[$key];
		}
		return false;
	}

} /*** end of class ***/
