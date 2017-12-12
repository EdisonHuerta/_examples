<?php
/**
 * File containing the skeleton controller
 *
 * @package SKF
 * @copyright Copyright (C) 2009 PHPRO.ORG. All rights reserved.
 *
 */

namespace skf;

class skeletonController extends baseController implements IController
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
		$this->view->layout_file = 'admin_layout.phtml'; 

		/*** a new view instance ***/
		$tpl = new view;

		/*** turn caching on for this page ***/
		$tpl->setCaching(false);

		/*** set the template dir ***/
		$tpl->setTemplateDir(APP_PATH . '/modules/skeleton/views');

		/*** the include template ***/
		$tpl->include_tpl = APP_PATH . '/views/skeleton/index.phtml';

		/*** a view variable ***/
		$this->view->title = 'Skeleton Builder';

		$tpl->alert_status = 'info';
		$tpl->alert_message = 'Use this form to generate a new system module';
		
		// did something get posted
		if( isset( $_POST['module_name'], $_POST['module_build'] ) )
		{
			// validate
			$val = new validation;
			$val->source = $_POST;
			$val->addValidator( array( 'name'=>'module_name', 'type'=>'string', 'required'=>true, 'min'=>1, 'max'=>50 ) );
			$val->run();
			if( sizeof( $val->errors ) > 0 )
			{
				$tpl->alert_status = 'danger';
				$tpl->alert_message = $val->makeErrorList();
			}
			else
			{
				// attempt to build the skeleton
				if( !is_writeable( APP_PATH.'/modules' ) )
				{
					$tpl->alert_status = 'danger';
					$tpl->alert_message =  APP_PATH .'/modules is not writable by the system!';
				}
				else
				{
					if( !mkdir( APP_PATH.'/modules/'.$val->sanitized['module_name'] ) )
					{
						$tpl->alert_status = 'danger';
						$tpl->alert_message =  'Unable to create module '.$val->sanitized['module_name'];
					}
					else
					{
						$module_name =  $val->sanitized['module_name'];
						$module_title =  ucfirst( $module_name );

						mkdir( APP_PATH . '/modules/' . $val->sanitized['module_name'] . '/controllers' );
						mkdir( APP_PATH . '/modules/' . $val->sanitized['module_name'] . '/config' );
						mkdir( APP_PATH.'/modules/'.$val->sanitized['module_name'].'/views' );
						mkdir( APP_PATH.'/modules/'.$val->sanitized['module_name'].'/assets' );
						mkdir( APP_PATH.'/modules/'.$val->sanitized['module_name'].'/assets/js' );
						mkdir( APP_PATH.'/modules/'.$val->sanitized['module_name'].'/assets/css' );
						mkdir( APP_PATH.'/modules/'.$val->sanitized['module_name'].'/tests' );
						mkdir( APP_PATH.'/modules/'.$val->sanitized['module_name'].'/tests/unit' );
						mkdir( APP_PATH.'/modules/'.$val->sanitized['module_name'].'/tests/selenium' );

						// write the main controller
						$name = $val->sanitized['module_name'].'controller.php';
						$path = APP_PATH . '/modules/' . $val->sanitized['module_name'] . '/controllers/'.$name;
						$text = file_get_contents( APP_PATH.'/modules/skeleton/controllers/skeleton_controller.txt' );
						$text = str_replace( 'MODULE_NAME', $val->sanitized['module_name'], $text );
						$text = str_replace( 'MODULE_TITLE', $module_title, $text );
						file_put_contents( $path, $text );

						// write the index view file
						$path = APP_PATH . '/modules/' . $val->sanitized['module_name'] . '/views/index.phtml';
						$text = file_get_contents( APP_PATH.'/modules/skeleton/controllers/index.txt' );
						$text = str_replace( 'MODULE_TITLE', $module_title, $text );
						$text = str_replace( 'MODULE_NAME', $module_name, $text );
						file_put_contents( $path, $text );

						// write the admin controller
						$name = $val->sanitized['module_name'].'_admincontroller.php';
						$path = APP_PATH . '/modules/' . $val->sanitized['module_name'] . '/controllers/'.$name;
						$text = file_get_contents( APP_PATH.'/modules/skeleton/controllers/skeleton_admin_controller.txt' );
						$text = str_replace( 'MODULE_NAME', $val->sanitized['module_name'], $text );
						$text = str_replace( 'MODULE_TITLE', $module_title, $text );
						$view_path = str_replace( 'VIEW_PATH', $val->sanitized['module_name'], $text );
						file_put_contents( $path, $text );
	
						// write the admin_index.phtml view file
						$path = APP_PATH . '/modules/' . $val->sanitized['module_name'] . '/views/admin_index.phtml';
						$text = file_get_contents( APP_PATH.'/modules/skeleton/controllers/admin_index.txt' );
						$text = str_replace( 'MODULE_NAME', $val->sanitized['module_name'], $text );
						$text = str_replace( 'MODULE_TITLE', $module_title, $text );
						file_put_contents( $path, $text );

						// write the admin controller view file
						$tpl->alert_status = 'success';
						$tpl->alert_message = $val->sanitized['module_name'] . ' module created successfully.';
					}
				}
			}

		}

		/*** the cache id is based on the file name ***/
		$cache_id = md5( 'skeleton/index.phtml' );

		/*** fetch the template ***/
		$this->content = $tpl->fetch( 'index.phtml', $cache_id);
	}
}
