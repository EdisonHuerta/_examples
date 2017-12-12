<?php

namespace skf;

class memcacher extends \memcached
{	
	/**
	*
	* Constructor, duh!
	* Calls parent then gets servers from config file
	* Creates array of memcached servers
	*
	* @access	public
	*
	*/
	public function __construct()
	{
		parent::__construct();

		$servers = config::getInstance()->config_values['memcached']['servers'];

		$mservers = array();
		
		foreach($servers as $s)
		{
			$mservers[] = explode(":", $s);
		}
		
		$this->addServers( $mservers );
	}

	/**
	*
	* Flush the cache
	*
	* @access	public
	* @return	bool
	*
	*/
   	public function flush( $delay=null )
   	{
   	 	return $this->flush();
   	}

	/**
	*
	* Add do cache
	*
	* @access	public
	* @param	int	$key
	* @param	mixed	$value
	* @param	int	$exp
	* @return	bool
	*
	*/
   	public function add( $key, $value, $exp=null )
   	{   		 
		return $this->add($key, $value, time() + $exp);	
   	}

	/**
	*
	* Set cache
	*
	* @access	public
	* @param	int	$key
	* @param	mixed	$value
	* @param	int	$exp
	*
	*/
   	public function set($key, $value, $exp=null)
   	{   
   		if(!$this->set($key, $value, time() + $exp))
   			$this->add($key, $value, time() + $exp);   	
   	}

	/**
	*
	* Delete
	*
	* @access	public
	* @param	int	$key
	* @return	bool
	*
	*/
   	public function del($key)
   	{
   		return $this->delete($key);   	
   	}

	/**
	*
	* Get cache
	*
	* @access	public
	* @param	int	$key
	* @return	string
	*
	*/
   	public function get( $key, $cache_cb = null, &$cas_token = null ) 
   	{
   		return $this->get($key);   		
   	}

	/**
	*
	* Update the cache expiry
	*
	* @access	public
	* @param	int	$key
	* @param	int	$exp
	* @return	bool
	*
	*/
   	public function updateExpire($key, $exp)
   	{   		 
		return $this->touch($key, time() + $exp);	
   	}

	/**
	*
	* Decrement cache $key
	*
	* @access	public
	* @param	int	$key
	* @return	bool
	*
	*/
   	public function dec($key)
   	{
   		return $this->decrement($key);
   	}

	/**
	*
	* Increment cache key
	* @access	public
	* @param	int	$key
	* @return	bool
	*
	*/
   	public function inc($key)
   	{
   		return $this->increment($key);
   	}

	/**
	*
	* Get status
	*
	* @access	public
	* @return	string
	*
	*/
   	public function status()
   	{
		return print_r( $this->getStats(), 1 );   	   
   	}
};
