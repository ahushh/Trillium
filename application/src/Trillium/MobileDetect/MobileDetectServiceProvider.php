<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\MobileDetect;

use Detection\MobileDetect;
use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * MobileDetectServiceProvider Class
 *
 * @package Trillium\MobileDetect
 */
class MobileDetectServiceProvider implements ServiceProviderInterface
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
        $app['mobiledetect.headers'] = [];
        $app['mobiledetect.user_agent'] = null;
        $app['mobiledetect'] = $app->share(function () use ($app) {

            return new MobileDetect($app['mobiledetect.headers'], $app['mobiledetect.user_agent']);
        });
        $app['mobiledetect.version'] = function () use ($app) {
            /** @var $detector MobileDetect */
            $detector = $app['mobiledetect'];

            return $detector->isMobile() ? 'mobile' : ($detector->isTablet() ? 'tablet' : null);
        };
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
