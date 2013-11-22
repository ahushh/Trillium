<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\Markup;


use FSHL\Highlighter;
use FSHL\Output\HtmlManual;
use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * MarkupServiceProvider Class
 *
 * @package Trillium\Markup
 */
class MarkupServiceProvider implements ServiceProviderInterface {

    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Application $app An Application instance
     */
    public function register(Application $app) {
        $app['markup'] = $app->share(function () use ($app) {
            return new Markup(new Highlighter(new HtmlManual()));
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