<?php

namespace skf;

class signup extends event{

	const USERNAME_TAKEN = 1;
	const USERNAME_TOO_SHORT = 2;
	const USERNAME_TOO_LONG = 3;
	const ALLOW = 4;

	public $status = array();
	public $username, $password, $ip_address;

	public function init()
	{
		// Let's simulate different signin conditions
		$this->setStatus( rand( 1, 4 ) );

		// Notify all the observers of a change
		$this->notify();

		if ( $this->status[0] == self::ALLOW ) {
			return true;
		}
		return false;
	}

	private function setStatus( $status ) {
		$this->status = array( $status, $this->username, $this->password, $this->ip_address );
	}

	public function getStatus() {
		return $this->status;
	}
}
