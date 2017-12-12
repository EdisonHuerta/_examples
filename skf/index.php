<?php

/**
 * File containing the index for system.
 *
 * @package SKF
 * @copyright Copyright (C) 2009 PHPRO.ORG. All rights reserved.
 * @filesource
 *
 */

namespace skf;

session_start();

// define the site path
$site_path = realpath(dirname(__FILE__));
define('SITE_PATH', $site_path);

// the application directory path 
define('APP_PATH', SITE_PATH . '/application');

// add the application to the include path
set_include_path(APP_PATH);
set_include_path(SITE_PATH);

// set the public web root path
$path = str_replace($_SERVER['DOCUMENT_ROOT'], '', SITE_PATH);
define('PUBLIC_PATH', $path);

spl_autoload_register(null, false);

spl_autoload_extensions('.php, .class.php, .lang.php');


try {

    // model loader
    function modelLoader($class) {
        $class = strtolower(str_replace('skf\\', '', $class));
        $models = array('icontroller.php', 'frontcontroller.php', 'view.php');
        $class = strtolower($class);
        $filename = $class . '.php';
        if (in_array($filename, $models)) {
            $file = APP_PATH . "/models/$filename";
        } else {
            $file = APP_PATH . "/$class/models/$filename";
        }
        if (file_exists($file) == false) {
            return false;
        }

        include_once $file;
    }

    // autoload controllers
    function controllerLoader($class) {
        $class = str_replace('skf\\', '', $class);

        if (substr($class, -16) == '_adminController') {
            $module = str_replace('_adminController', '', $class);
            $filename = $module . '_admincontroller.php';
        } else {
            $module = str_replace('Controller', '', $class);
            $filename = $class . '.php';
        }
        $file = strtolower(APP_PATH . "/modules/$module/controllers/$filename");
        if (file_exists($file) == false) {
            return false;
        }
        include_once $file;
    }

    // autoload libs
    function libLoader($class) {
        $class = str_replace('skf\\', '', $class);
        $filename = strtolower($class) . '.class.php';
        // hack to remove namespace 
        $file = APP_PATH . '/lib/' . $filename;
        if (file_exists($file) == false) {
            return false;
        }
        include_once $file;
    }

    // autoload DAO
    function daoLoader($class) {
        if ($class == 'dao') {
            echo 'alala';
        }

        $class = str_replace('skf\\', '', $class);

        logger::debugLog("DAO class is $class", 300, __METHOD__, __LINE__);
        $filename = strtolower($class) . '.class.php';

        logger::debugLog("DAO file $filename", 300, __METHOD__, __LINE__);
        // hack to remove namespace 
        $file = APP_PATH . '/lib/objects/' . $filename;
        if (file_exists($file) == false) {
            logger::debugLog("DAO file $file does not exist", 200, __METHOD__, __LINE__);
            return false;
        }

        logger::debugLog("Loading DAO file $file", 200, __METHOD__, __LINE__);
        include_once $file;
    }

    spl_autoload_register(__NAMESPACE__ . '\libLoader');
    spl_autoload_register(__NAMESPACE__ . '\modelLoader');
    spl_autoload_register(__NAMESPACE__ . '\controllerLoader');
    spl_autoload_register(__NAMESPACE__ . '\daoLoader');

    $config = new config;
    $lang = $config->config_values['application']['language'];
    $filename = strtolower($lang) . '.lang.php';
    $file = APP_PATH . '/lang/' . $filename;
    include $file;

    // alias the lang class
    class_alias(__NAMESPACE__ . '\\' . $lang, __NAMESPACE__ . '\lang');

    // set the domain status
    // $domain_config = domain_config::getInstance();
// var_dump( $domain_config );
    // set the timezone
    date_default_timezone_set($config->config_values['application']['timezone']);

    /**
     *
     * @custom error function to throw exception
     *
     * @param int $errno The error number
     *
     * @param string $errmsg The error message
     *
     */
    function skfErrorHandler($errno, $errmsg) {
        throw new skfException($errmsg, $errno);
    }

    /*     * * set error handler level to E_WARNING ** */
    // set_error_handler('skfErrorHandler', $config->config_values['application']['error_reporting']);
    // Initialize the FrontController
    $front = new FrontController;
    $front->route();
//	echo $front->getBody();
} catch (skfException $e) {
    throw new \Exception($e);
}
// catch exceptions from the php exception class
catch (\Exception $e) {
    // if we are here, we are the top of pond for exceptions to bubble up
    // here we send off to a page for system errors
    $front = new FrontController;
    include_once APP_PATH . '/models/view.php';
    $tpl = new view;
    $tpl->setTemplateDir(APP_PATH . "/layouts");
    $tpl->error_message = $e->getMessage();
    $tpl->error_line = $e->getLine();
    $tpl->error_file = $e->getFile();
    $tpl->backtrace = $e->getTraceAsString();
    $tpl->version = $config->config_values['application']['version'];
    $tpl->title = 'System Error!';
    $tpl->content = $tpl->getOutput(APP_PATH . '/modules/error500/views/index.phtml');
    $tpl->render("index.phtml");
}
