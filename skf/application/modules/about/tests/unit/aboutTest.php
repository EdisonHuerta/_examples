<?php

$_SERVER['QUERY_STRING'] = '/about';
require_once 'PHPUnit/Framework.php';

require_once 'init/init.php';
require_once APP_PATH.'/modules/about/controllers/aboutcontroller.php';
require_once APP_PATH.'/modules/blog/controllers/blogcontroller.php';

class aboutTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers aboutController::index
	 */
	public function testNewArrayIsEmpty()
	{
		// Create the Array fixture.
		$fixture = array();

		$_SERVER['QUERY_STRING'] = 'about';
		$_SERVER['REQUEST_URI'] = '/about';
		$_SERVER['SERVER_PROTOCOL'] = 'http';
		$_SERVER['HTTP_HOST'] = 'localhost';

		$about = new skf\aboutController; 

		$this->assertTrue( method_exists( $about, 'index' ) );
	}

} // end of class
?>
