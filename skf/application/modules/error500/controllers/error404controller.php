<?php

namespace skf;

class error500Controller extends baseController implements IController
{
	/**
	*
	* Constructor, duh
	*
	*/
	public function __construct()
	{
		parent::__construct();
	}

	/**
	*
	* The index function
	*
	* @access	public
	*
	*/
	public function index()
	{
		/*** a new view instance ***/
		$tpl = new view;

		/*** turn caching on for this page ***/
		// $view->setCaching(true);

		/*** set the template dir ***/
		$tpl->setTemplateDir(APP_PATH.'/modules/error500/views');

		/*** a view variable ***/
		$this->view->title = '500 File Not Found';
		$this->view->heading = '500 File Not Found';

                // a new config
                $config = new config;
                $this->view->version = $config->config_values['application']['version'];

		/*** the cache id is based on the file name ***/
		$cache_id = md5( '500/index.php' );

		/*** fetch the template ***/
		$this->content = $tpl->fetch( 'index.phtml', $cache_id);
	}

}
