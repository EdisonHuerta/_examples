<?php
/**
 * File containing the index controller
 *
 * @package SKF
 * @copyright Copyright (C) 2009 PHPRO.ORG. All rights reserved.
 *
 */

namespace skf;

class indexController extends baseController implements IController
{

	public function __construct()
	{
		parent::__construct();
		// a new config
		$config = new config;
		$this->view->version = $config->config_values['application']['version'];
	}

	public function index()
	{
		/*** a new view instance ***/
		$tpl = new view;

		/*** turn caching on for this page ***/
		$tpl->setCaching(true);

		/*** set the template dir ***/
		$tpl->setTemplateDir(APP_PATH . '/modules/index/views');

		/*** the include template ***/
		$tpl->include_tpl = APP_PATH . '/views/index/index.phtml';

		/*** a view variable ***/
		$this->view->title = 'SKF Framework';

		$this->view->current_menu_item = 'index';

		/*** the cache id is based on the file name ***/
		$cache_id = md5( 'index/index.phtml' );

		/*** fetch the template ***/
		$this->content = $tpl->fetch( 'index.phtml', $cache_id);
	}


	public function signup()
	{
		// a new event
		$signup = new signup;
		$signup->username = 'freddy';
		$signup->password = 'queen';
		$signup->ip_address = '127.0.0.1';

		// Attach classes to observer/listen for a login event
		$signup_emailer = new signupemailer;
		$signup->attach( $signup_emailer );
		// $signup->attach( new signupemailer );

	       /*** a new view instance ***/
		$tpl = new view;

		/*** turn caching on for this page ***/
		$tpl->setCaching(true);

		/*** set the template dir ***/
		$tpl->setTemplateDir(APP_PATH . '/modules/index/views');

		/*** the include template ***/
		$tpl->include_tpl = APP_PATH . '/views/index/signup.phtml';


		// check here for signup message
		if( $signup->init() !== false )
		{
			$tpl->message = "Signup success";
		}
		else
		{
			$tpl->message = print_r( $signup->status, 1 );
		}
		$tpl->signup_message = $signup_emailer->signup_message;


	       /*** a view variable ***/
		$this->view->title = 'SKF - Events';
		$this->view->heading = 'SKF Events';

		/*** the cache id is based on the file name ***/
		$cache_id = md5( 'index/signup.phtml' );

		/*** fetch the template ***/
		$this->content = $tpl->fetch( 'signup.phtml', $cache_id);
	}

	public function test()
	{
		$view = new view;
		$view->text = 'this is a test';
		$result = $view->fetch( APP_PATH.'/views/index.php' );
		$fc = new FrontController;
		$fc->setBody($result);
	}
}
