<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\ImageBoard;

use FSHL\Highlighter;
use FSHL\Output\HtmlManual;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Trillium\ImageBoard\Service\Board;
use Trillium\ImageBoard\Service\Common;
use Trillium\ImageBoard\Service\Image;
use Trillium\ImageBoard\Service\Markup;
use Trillium\ImageBoard\Service\Post;
use Trillium\ImageBoard\Service\Thread;

/**
 * ImageBoardServiceProvider Class
 *
 * @package Trillium\ImageBoard
 */
class ImageBoardServiceProvider implements ServiceProviderInterface {

    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Application $app An Application instance
     */
    public function register(Application $app) {
        $app['imageboard.resources_path'] = null;
        $app['imageboard.board'] = $app->share(function () use ($app) {
            return new Board($app['model.mysqli']);
        });
        $app['imageboard.thread'] = $app->share(function () use ($app) {
            return new Thread($app['model.mysqli']);
        });
        $app['imageboard.post'] = $app->share(function () use ($app) {
            return new Post($app['model.mysqli']);
        });
        $app['imageboard.image'] = $app->share(function () use($app) {
            return new Image($app['model.mysqli']);
        });
        $app['imageboard.markup'] = $app->share(function () {
            return new Markup(new Highlighter(new HtmlManual()));
        });
        $app['imageboard.common'] = $app->share(function () use ($app) {
            return new Common($app, $app['imageboard.board'], $app['imageboard.thread'], $app['imageboard.post'], $app['imageboard.image']);
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