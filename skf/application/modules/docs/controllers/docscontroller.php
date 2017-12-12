<?php

namespace skf;

class docsController extends baseController implements IController
{
	/**
	*
	* Constructor, duh
	*
	*/
	public function __construct()
	{
		parent::__construct();

		// set the template directory
		$this->tpl = new view;
		$this->tpl->setTemplateDir(APP_PATH.'/modules/docs/views');

		$this->view->current_menu_item = 'docs';

		// a new config
		$config = new config;
		$this->view->version = $config->config_values['application']['version'];
	}

	/**
	*
	* The index function displays the login form
	*
	*/
	public function index()
	{
		/*** a view variable ***/
		$this->view->title = 'SKF Blog';
		$this->view->heading = 'SKF Blog';

		/*** the cache id is based on the file name ***/
		$cache_id = md5( 'docs/index' );

		/*** fetch the template ***/
		$this->content = $this->tpl->fetch( 'index.phtml', $cache_id);
	}

	public function configuration()
        {
                /*** a view variable ***/
                $this->view->title = 'SKF Configuration';
                $this->view->heading = 'Configuration';

                /*** the cache id is based on the file name ***/
                $cache_id = md5( 'docs/configuration.php' );

                /*** fetch the template ***/
                $this->content = $this->tpl->fetch( 'configuration.phtml', $cache_id);

	}


        public function constants()
        {
                /*** a view variable ***/
                $this->view->title = 'SKF Constants';
                $this->view->heading = 'Constants';

                /*** fetch the template ***/
                $this->content = $this->tpl->fetch( 'constants.phtml' );
        }


        public function controllers()
        {
                /*** a view variable ***/
                $this->view->title = 'SKF Controllers';
                $this->view->heading = 'Controllers';

                /*** fetch the template ***/
                $this->content = $this->tpl->fetch( 'controllers.phtml' );
        }


        public function database()
        {
                /*** a view variable ***/
                $this->view->title = 'SKF Database';
                $this->view->heading = 'Database';

                /*** fetch the template ***/
                $this->content = $this->tpl->fetch( 'database.phtml' );
        }


        public function htaccess()
        {
                /*** a view variable ***/
                $this->view->title = 'SKF htaccess';
                $this->view->heading = '.htaccess';

                /*** fetch the template ***/
                $this->content = $this->tpl->fetch( 'htaccess.phtml' );
        }

        public function rss()
        {
                /*** a view variable ***/
                $this->view->title = 'SKF RSS';
                $this->view->heading = 'RSS';

                /*** fetch the template ***/
                $this->content = $this->tpl->fetch( 'rss.phtml' );
        }

        public function mail()
        {
                /*** a view variable ***/
                $this->view->title = 'SKF Mail';
                $this->view->heading = 'Mail';

                /*** fetch the template ***/
                $this->content = $this->tpl->fetch( 'mail.phtml' );
        }


        public function lang()
        {
                /*** a view variable ***/
                $this->view->title = 'SKF Language';
                $this->view->heading = 'Language';

                /*** fetch the template ***/
                $this->content = $this->tpl->fetch( 'language.phtml' );
        }

        public function logging()
        {
                /*** a view variable ***/
                $this->view->title = 'SKF Logging';
                $this->view->heading = 'Logging';

                /*** fetch the template ***/
                $this->content = $this->tpl->fetch( 'logging.phtml' );
        }

        public function namespaces()
        {
                /*** a view variable ***/
                $this->view->title = 'SKF Namespaces';
                $this->view->heading = 'Namespaces';

                /*** fetch the template ***/
                $this->content = $this->tpl->fetch( 'namespaces.phtml' );
        }

        public function modules()
        {
                /*** a view variable ***/
                $this->view->title = 'SKF Modules';
                $this->view->heading = 'Modules';

                /*** fetch the template ***/
                $this->content = $this->tpl->fetch( 'modules.phtml' );
        }

        public function caching_and_templating()
        {
                /*** a view variable ***/
                $this->view->title = 'SKF Caching and Templating';
                $this->view->heading = 'Caching and Templating';

                /*** fetch the template ***/
                $this->content = $this->tpl->fetch( 'caching_and_templating.phtml' );
        }

        public function uri()
        {
                /*** a view variable ***/
                $this->view->title = 'SKF URI';
                $this->view->heading = 'URI';

                /*** fetch the template ***/
                $this->content = $this->tpl->fetch( 'uri.phtml' );
        }

        public function validation()
        {
                /*** a view variable ***/
                $this->view->title = 'SKF Validation';
                $this->view->heading = 'Validation';

                /*** fetch the template ***/
                $this->content = $this->tpl->fetch( 'validation.phtml' );
        }

        public function view()
        {
                /*** a view variable ***/
                $this->view->title = 'SKF View';
                $this->view->heading = 'View';

                /*** fetch the template ***/
                $this->content = $this->tpl->fetch( 'view.phtml' );
        }

	public function snorg()
	{
		$this->view->title = 'SKF View';
		$this->view->heading = 'View';
		$this->content = $this->tpl->fetch( 'snorg.phtml' );
	}

	public function events()
	{
		$this->view->title = 'SKF Events';
		$this->view->heading = 'View';
		$this->content = $this->tpl->fetch( 'events.phtml' );
	}

	public function assets()
	{
		$this->view->title = 'SKF Assets';
		$this->view->heading = 'View';
		$this->content = $this->tpl->fetch( 'assets.phtml' );
	}

	public function layouts()
	{
		$this->view->title = 'SKF Layouts';
		$this->view->heading = 'View';
		$this->content = $this->tpl->fetch( 'layouts.phtml' );
	}
} // end of class
