<?php

/**
 * @author Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/*
 * Set error reporting level
 */
error_reporting( E_ALL | E_STRICT );

/**
 * Default timezone
 */
date_default_timezone_set('Europe/London');

/*
 * Set the include path
 */
$root  = realpath(dirname(__FILE__) . '/../');
$paths = array(
    get_include_path(),
    "$root/library",
    "$root/tests",
    "$root/application"
);
set_include_path(implode(PATH_SEPARATOR, $paths));

defined('APPLICATION_PATH')
    or define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

/**
 * Get the base test case
 */
require_once 'ControllerTestCase.php';

require_once 'Zend/Loader/Autoloader.php';
$loader = Zend_Loader_Autoloader::getInstance();
$loader->registerNamespace('BBA_');
$loader->registerNamespace('ZendSF_');

/**
 * Start session now!
 */
Zend_Session::$_unitTestEnabled = true;
Zend_Session::start();

?>