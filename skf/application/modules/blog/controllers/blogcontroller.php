<?php
/**
 * File containing the blog controller
 *
 * @package SKF
 * @copyright Copyright (C) 2009 PHPRO.ORG. All rights reserved.
 *
 */

namespace skf;

class blogController extends baseController implements IController
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
		$tpl->setTemplateDir(APP_PATH . '/modules/blog/views');

		/*** a view variable ***/
		$this->view->title = 'Blog';

		$this->view->current_menu_item = 'blog';

		/*** the cache id is based on the file name ***/
		$cache_id = md5( 'blog/index.phtml' );

		/*** fetch the template ***/
		$this->content = $tpl->fetch( 'index.phtml', $cache_id);
	}

	public function show()
	{
		/*** a new view instance ***/
		$tpl = new view;

		/*** turn caching on for this page ***/
		$tpl->setCaching(true);

		/*** set the template dir ***/
		$tpl->setTemplateDir(APP_PATH . '/modules/blog/views');

		/*** a view variable ***/
		$this->view->title = 'Blog';

		$this->view->current_menu_item = 'Blog';

		/*** the cache id is based on the file name ***/
		$cache_id = md5( 'blog/show.phtml' );

		/*** fetch the template ***/
		$this->content = $tpl->fetch( 'show.phtml', $cache_id);
	}
} // end of class
