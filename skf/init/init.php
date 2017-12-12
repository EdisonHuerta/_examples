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

// define the site path
// $site_path = realpath( dirname(__FILE__) );
$site_path = realpath(dirname('..'));
define('SITE_PATH', $site_path);

// the application directory path 
define('APP_PATH', SITE_PATH . '/application');

include_once APP_PATH . '/lib/uri.class.php';
include_once APP_PATH . '/lib/config.class.php';
include_once APP_PATH . '/lib/logger.class.php';
include_once APP_PATH . '/modules/base/controllers/basecontroller.php';

$config = new config;

date_default_timezone_set($config->config_values['application']['timezone']);

// add the application to the include path
set_include_path(APP_PATH);
set_include_path(SITE_PATH);

// set the public web root path
$path = str_replace($_SERVER['DOCUMENT_ROOT'], '', SITE_PATH);
define('PUBLIC_PATH', $path);

spl_autoload_register(null, false);

spl_autoload_extensions('.php, .class.php, .lang.php');

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
        $filename = $class . 'controller.php';
    }
    $file = strtolower(APP_PATH . "/modules/$module/controllers/$filename");
    echo $file . "\n";
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
