<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\Model;

use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * ModelServiceProvider Class
 *
 * Provider for the models
 *
 * @package Trillium\Model
 */
class ModelServiceProvider implements ServiceProviderInterface {

    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Application $app An Application instance
     */
    public function register(Application $app) {
        $app['model.mysqli'] = $app->share(function () use ($app) {
            $mysqli = new MySQLi($app['mysqli.host'], $app['mysqli.user'], $app['mysqli.password'], $app['mysqli.database']);
            $mysqli->set_charset($app['mysqli.charset']);
            return $mysqli;
        });
        $app['model'] = $app->protect(function ($name) use ($app) {
            $className = '\Application\Model\\' . ucwords($name);
            $object = new $className($app['model.mysqli']);
            if (!($object instanceof Model)) {
                throw new \RuntimeException('Object of the model should be instance of \trillium\Model');
            }
            return $object;
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