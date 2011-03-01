<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
if(!defined('APPLICATION_ENV')) {
  if(getenv('APPLICATION_ENV')) {
    define('APPLICATION_ENV', getenv('APPLICATION_ENV'));
  } else if('inserate.local' == $_SERVER['SERVER_NAME']) {
    define('APPLICATION_ENV', 'development');
  } else if('mobile.local' == $_SERVER['SERVER_NAME']) {
    define('APPLICATION_ENV', 'development');
  } else if('inserate.opentution.org' == $_SERVER['SERVER_NAME']) {
    define('APPLICATION_ENV', 'testing');
  } else if('dev.politinserate.at' == $_SERVER['SERVER_NAME']) {
    define('APPLICATION_ENV', 'testing');
  } else if('mdev.politinserate.at' == $_SERVER['SERVER_NAME']) {
    define('APPLICATION_ENV', 'testing');
  } else {
    define('APPLICATION_ENV', 'production');
  }
}

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()
            ->run();