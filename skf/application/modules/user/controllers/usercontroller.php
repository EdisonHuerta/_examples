<?php
/**
 * File containing the user controller
 *
 * @package SKF
 * @copyright Copyright (C) 2009 PHPRO.ORG. All rights reserved.
 *
 */

namespace skf;

class userController extends baseController implements IController
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
		$tpl->setTemplateDir(APP_PATH . '/modules/user/views');

		/*** the include template ***/
		// $tpl->include_tpl = APP_PATH . '/views/user/index.phtml';

		/*** a view variable ***/
		$this->view->title = 'User';

		/*** the cache id is based on the file name ***/
		$cache_id = md5( 'user/index.phtml' );

		/*** fetch the template ***/
		$this->content = $tpl->fetch( 'index.phtml', $cache_id);
	}

	public function signup()
	{
		/*** a new view instance ***/
		$tpl = new view;

		/*** turn caching on for this page ***/
		$tpl->setCaching(true);

		/*** set the template dir ***/
		$tpl->setTemplateDir(APP_PATH . '/modules/user/views');

		/*** the include template ***/
		// $tpl->include_tpl = APP_PATH . '/views/user/index.phtml';

		/*** a view variable ***/
		$this->view->title = 'Sign Up';

		/*** the cache id is based on the file name ***/
		$cache_id = md5( 'user/signup.phtml' );

		/*** fetch the template ***/
		$this->content = $tpl->fetch( 'signup.phtml', $cache_id);
	}

	public function login()
	{
		/*** a new view instance ***/
		$tpl = new view;

		/*** turn caching on for this page ***/
		$tpl->setCaching(true);

		/*** set the template dir ***/
		$tpl->setTemplateDir(APP_PATH . '/modules/user/views');

		/*** a view variable ***/
		$this->view->title = 'User Login';

		/*** the cache id is based on the file name ***/
		$cache_id = md5( 'user/login.phtml' );

		/*** fetch the template ***/
		$this->content = $tpl->fetch( 'login.phtml', $cache_id);
	}
}
