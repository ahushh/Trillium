<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\MySQLi;

use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * MySQLiServiceProvider Class
 *
 * Provider for the MySQLi class
 *
 * @package Trillium\MySQLi
 */
class MySQLiServiceProvider implements ServiceProviderInterface {

    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Application $app An Application instance
     */
    public function register(Application $app) {
        $app['mysqli'] = $app->share(function () use ($app) {
            $mysqli = new MySQLi($app['mysqli.host'], $app['mysqli.user'], $app['mysqli.password'], $app['mysqli.database']);
            $mysqli->set_charset($app['mysqli.charset']);
            return $mysqli;
        });
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     */
    public function boot(Application $app) {
    }

}