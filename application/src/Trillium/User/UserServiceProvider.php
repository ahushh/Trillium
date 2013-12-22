<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\User;

use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * UserServiceProvider Class
 *
 * Registers user.manager and user.roles services
 *
 * @package Trillium\User
 */
class UserServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Application $app An Application instance
     */
    public function register(Application $app)
    {
        $app['user.manager'] = $app->share(function () use ($app) {

            return new UserManager($app['mysqli'], $app['security.encoder_factory']);
        });
        $app['user.roles'] = !empty($app['user.roles']) ? $app['user.roles'] : [];
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     */
    public function boot(Application $app)
    {
    }

}
