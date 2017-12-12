<?php
/**
 * File containing the setup controller
 *
 * @package SKF
 * @copyright Copyright (C) 2009 PHPRO.ORG. All rights reserved.
 *
 */

namespace skf;

class setupController extends baseController implements IController
{

	public function __construct()
	{
		parent::__construct();
		// a new config
		$this->config = new config;
		$this->view->version = $this->config->config_values['application']['version'];
	}

	public function index()
	{
		/*** a new view instance ***/
		$tpl = new view;

		/*** turn caching on for this page ***/
		$tpl->setCaching(true);

		/*** set the template dir ***/
		$tpl->setTemplateDir(APP_PATH . '/modules/setup/views');

		/*** the include template ***/
		// $tpl->include_tpl = APP_PATH . '/views/setup/index.phtml';

		/*** a view variable ***/
		$this->view->title = 'Setup';

		$msg = '';

		// check modules directory, cache directory, and sqlite directory are writable
		if( !is_writable( APP_PATH.'/modules' ) )
		{
			$msg .= 'Modules directory '. APP_PATH.'/modules is not writable by the system. You must alter the permissions of this directory to continue with setup';
		}
		elseif( !is_writable( $this->config->config_values['template']['cache_dir'] ) )
		{
			$msg .= 'Cache directory '. $this->config->config_values['template']['cache_dir'] . ' is not writable by the system. You must alter the permissions of this directory to continue with setup';
		}
		else
		{
			$msg = 'Permissions check OK!';
		}

		$tpl->msg = $msg;
		// check modules directory, cache directory, and sqlite directory are writable

		//create database sfk;

		//Grant db permissions;

		// Test database
		// test sqlite directory is writeable if using sqlite

		// Test modules directory is writeable and if not warn user that cannot create modules

		/*** the cache id is based on the file name ***/
		$cache_id = md5( 'setup/index.phtml' );

		/*** fetch the template ***/
		$this->content = $tpl->fetch( 'index.phtml', $cache_id);
	}


	public function database()
	{
		/*** a new view instance ***/
		$tpl = new view;

		/*** turn caching on for this page ***/
		$tpl->setCaching(true);

		/*** set the template dir ***/
		$tpl->setTemplateDir(APP_PATH . '/modules/setup/views');

		/*** the include template ***/
		// $tpl->include_tpl = APP_PATH . '/views/setup/index.phtml';

		/*** a view variable ***/
		$this->view->title = 'Setup Database';

		$msg = '';

		// did something get POSTed?
		if( isset( $_POST['database_type'] ) )
		{
			// validate the db type
			$val = new \skf\validation;
			$val->source = $_POST;
			$val->addValidator( array( 'name'=>'database_type', 'type'=>'select', 'required'=>true, 'values'=>array( 'mysql', 'pgsql', 'sqlite', 'nodb'  ) ) );
			$val->run();

			$msg = '';

			// do we have an error?
			if( sizeof( $val->errors ) > 0 )
			{
				foreach( $val->errors as $err )
				{
					$msg .= $err.'<br />';
				}
			}
			else
			{
				$msg = 'success';
				// are we using a database for this set up?
				$_SESSION['setup']['database_type'] = $val->sanitized['database_type'];

				switch( $val->sanitized['database_type'] )
				{
					case 'mysql':
						$res = $this->createDb( 'mysql' );
						if( $res != true )
						{
							$msg = $res;
						}
						else
						{
							$msg = 'mysql database set up';
						}
						break;

					case 'pgsql':
						$res = $this->createDb( 'pgsql' );
						if( $res != true )
						{
							$msg = $res;
						}
						else
						{
							$msg = 'PGsql database set up';
						}
						break;

					case 'sqlite':
						if( !is_writable( APP_PATH.'/'.$this->config->config_values['database']['db_sqlite_path'] ) )
						{
							$msg = 'The SQLite directory '.APP_PATH.'/'.$this->config->config_values['database']['db_sqlite_path'].' is not writable. Check the db_sqlite_path configuration setting and adjust the file permissions';
						}
						else
						{
							$this->createDb( 'sqlite' );
							$msg = 'SQLite database created succesfully ';
						}
						break;
					case 'nodb':
						$msg = 'No Database will be created for this installation';
						break;

					default: throw new \Exception( $val->sanitized['database_type'] . ' is not a valid option' );
				}
			}
		}

		$tpl->msg = $msg;

		/*** the cache id is based on the file name ***/
		$cache_id = md5( 'setup/database.phtml' );

		/*** fetch the template ***/
		$this->content = $tpl->fetch( 'database.phtml', $cache_id);
	}



	private function createDb()
	{
		try
		{
			$db = new db;
		}
		catch( \PDOException $e )
		{
			return $e->getMessage();
		}
		return true;
	}

} // end of class
