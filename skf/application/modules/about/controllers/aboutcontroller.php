<?php
/**
 * File containing the about controller
 *
 * @package SKF
 * @copyright Copyright (C) 2009 PHPRO.ORG. All rights reserved.
 *
 */

namespace skf;

class aboutController extends baseController implements IController
{

	public function __construct()
	{
		parent::__construct();
		// a new config
		$config = new config;
		$this->view->version = $config->config_values['application']['version'];
		$this->view->current_menu_item = 'about';
	}

	public function index()
	{
		/*** a new view instance ***/
		$tpl = new view;

		/*** turn caching on for this page ***/
		$tpl->setCaching(true);

		/*** set the template dir ***/
		$tpl->setTemplateDir(APP_PATH . '/modules/about/views');

		/*** a view variable ***/
		$this->view->title = 'About';

		/*** the cache id is based on the file name ***/
		$cache_id = md5( 'about/index.phtml' );

		/*** fetch the template ***/
		$this->content = $tpl->fetch( 'index.phtml', $cache_id);
	}

	public function fullwidth()
	{
		/*** a new view instance ***/
		$tpl = new view;

		/*** turn caching on for this page ***/
		$tpl->setCaching(true);

		/*** set the template dir ***/
		$tpl->setTemplateDir(APP_PATH . '/modules/about/views');

		$this->view->title = 'Full Width';

		/*** the cache id is based on the file name ***/
		$cache_id = md5( 'about/fullwidth.phtml' );

		/*** fetch the template ***/
		$this->content = $tpl->fetch( 'fullwidth.phtml', $cache_id);
	}

	public function features()
	{
		/*** a new view instance ***/
		$tpl = new view;

		/*** turn caching on for this page ***/
		$tpl->setCaching(true);

		/*** set the template dir ***/
		$tpl->setTemplateDir(APP_PATH . '/modules/about/views');

		/*** a view variable ***/
		$this->view->title = 'Features';

		/*** the cache id is based on the file name ***/
		$cache_id = md5( 'about/features.phtml' );

		/*** fetch the template ***/
		$this->content = $tpl->fetch( 'features.phtml', $cache_id);
	}

	public function typography()
	{
		/*** a new view instance ***/
		$tpl = new view;

		/*** turn caching on for this page ***/
		$tpl->setCaching(true);

		/*** set the template dir ***/
		$tpl->setTemplateDir(APP_PATH . '/modules/about/views');

		/*** a view variable ***/
		$this->view->title = 'Typography';

		/*** the cache id is based on the file name ***/
		$cache_id = md5( 'about/typography.phtml' );

		/*** fetch the template ***/
		$this->content = $tpl->fetch( 'typography.phtml', $cache_id);
	}

	public function icons()
	{
		/*** a new view instance ***/
		$tpl = new view;

		/*** turn caching on for this page ***/
		$tpl->setCaching(true);

		/*** set the template dir ***/
		$tpl->setTemplateDir(APP_PATH . '/modules/about/views');

		/*** a view variable ***/
		$this->view->title = 'Icons';

		/*** the cache id is based on the file name ***/
		$cache_id = md5( 'about/icons.phtml' );

		/*** fetch the template ***/
		$this->content = $tpl->fetch( 'icons.phtml', $cache_id);
	}

} // end of class
