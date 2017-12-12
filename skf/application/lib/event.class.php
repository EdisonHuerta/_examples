<?php

/**
 *
 * @Class to create events
 *
 * @copyright Copyright (C) 2009 PHPRO.ORG. All rights reserved.
 *
 * @license new bsd http://www.opensource.org/licenses/bsd-license.php
 * @package Files
 * @Author Kevin Waterson
 *
 */

namespace skf;

class event implements \SplSubject {

	private $pre_storage, $post_storage;

	const PRE_EVENT = 1;
	const POST_EVENT = 2;

	public function __construct() {
		$this->pre_storage = new \SplObjectStorage();
		$this->post_storage = new \SplObjectStorage();
	}

	public function init()
	{
		// Notify all the observers of a change
		$this->notify( );
	}

	/*
	 *
	 * @access	public
	 * @param	SplObserver	$observer
	 * @param	integer		$event_condition
	 *
	 */
	public function attach( \SplObserver $observer, $event_condition=2 )
	{
		switch ( $event_condition )
		{
			case 1:
				$this->pre_storage->attach( $observer );
				break;

			case 2:
				$this->post_storage->attach( $observer );
				break;

			default:
				throw new Exception("Storage type not available");
		}
	}

	public function detach( \SplObserver $observer ) {
		$this->storage->detach( $observer );
	}

	public function notify()
	{
		foreach ( $this->pre_storage as $observer )
		{
			$observer->update( $this );
		}
		foreach ( $this->post_storage as $observer )
		{
			$observer->update( $this );
		}
	}
} // end events class

?>
