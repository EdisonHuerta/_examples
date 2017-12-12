<?php

$_SERVER['QUERY_STRING'] = '/blog';
require_once '/usr/share/pear/PHPUnit/Framework.php';

require_once 'init/init.php';
require_once APP_PATH.'/modules/blog/controllers/blogcontroller.php';

class blogTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers blogController::index
	 */
	public function testNewArrayIsEmpty()
	{
		// Create the Array fixture.
		$fixture = array();

		$_SERVER['QUERY_STRING'] = 'blog';
		$_SERVER['REQUEST_URI'] = '/blog';
		$_SERVER['SERVER_PROTOCOL'] = 'http';
		$_SERVER['HTTP_HOST'] = 'localhost';

		$blog = new skf\blogController; 

		$this->assertTrue( method_exists( $blog, 'index' ) );
	}

} // end of class
?>
