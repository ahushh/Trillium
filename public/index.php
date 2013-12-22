<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

/**
 * Trillium environment: "dev" or "prod"
 */
define('TRILLIUM_ENVIRONMENT', 'dev');
//define('TRILLIUM_ENVIRONMENT', 'prod');

/**
 * Alias for directory separator
 */
define('DS', DIRECTORY_SEPARATOR);

/**
 * Path to the sources directory
 */
define('SRC_DIR', realpath(__DIR__ . DS . '..' . DS) . DS);

/**
 * Path to the application directory
 */
define('APP_DIR', SRC_DIR . 'application' . DS);

/**
 * Path to the configuration files directory.
 * Defined with considering the environment.
 */
define('CONFIG_DIR', APP_DIR . 'config' . DS . TRILLIUM_ENVIRONMENT . DS);

/**
 * Path to the views directory
 */
define('VIEWS_DIR', APP_DIR . 'views' . DS);

/**
 * Path to the locales directory
 */
define('LOCALES_DIR', APP_DIR . 'locales' . DS);

/**
 * Path to the logs directory
 */
define('LOGS_DIR', APP_DIR . 'logs' . DS);

/**
 * Path to the cache
 */
define('CACHE_DIR', APP_DIR . 'cache' . DS);

/**
 * Path to the resources
 */
define('RESOURCES_DIR', APP_DIR . 'resources' . DS);

/**
 * Path to the assets web directory
 */
define('ASSETS_WEB_DIR', __DIR__ . DS . 'assets' . DS);

ini_set('display_errors', true);
error_reporting(-1);

require SRC_DIR . 'vendor' . DS . 'autoload.php';
require APP_DIR . 'application.php';
