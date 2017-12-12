<?php
/**
 * File containing the portfolio controller
 *
 * @package SKF
 * @copyright Copyright (C) 2009 PHPRO.ORG. All rights reserved.
 *
 */

namespace skf;

class portfolioController extends baseController implements IController
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
		$tpl->setTemplateDir(APP_PATH . '/modules/portfolio/views');

		/*** the include template ***/
		// $tpl->include_tpl = APP_PATH . '/views/portfolio/index.phtml';

		$this->view->current_menu_item = 'portfolio';

		/*** a view variable ***/
		$this->view->title = 'Portfolio';

		/*** the cache id is based on the file name ***/
		$cache_id = md5( 'portfolio/index.phtml' );

		/*** fetch the template ***/
		$this->content = $tpl->fetch( 'index.phtml', $cache_id);
	}
}
