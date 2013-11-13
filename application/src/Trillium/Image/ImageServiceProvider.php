<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\Image;

use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * ImageServiceProvider Class
 *
 * @package Trillium\Image
 */
class ImageServiceProvider implements ServiceProviderInterface {

    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Application $app An Application instance
     */
    public function register(Application $app) {
        $app['image'] = $app->protect(function ($path) {
            return new ImageService($path);
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