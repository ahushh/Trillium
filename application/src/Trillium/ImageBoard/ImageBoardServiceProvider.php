<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\ImageBoard;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Trillium\ImageBoard\Service\Board;
use Trillium\ImageBoard\Service\Common;
use Trillium\ImageBoard\Service\Image;
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
            return new Board(new Model\Board($app['model.mysqli']));
        });
        $app['imageboard.thread'] = $app->share(function () use ($app) {
            return new Thread(new Model\Thread($app['model.mysqli']));
        });
        $app['imageboard.post'] = $app->share(function () use ($app) {
            return new Post(new Model\Post($app['model.mysqli']));
        });
        $app['imageboard.image'] = $app->share(function () use($app) {
            return new Image(new Model\Image($app['model.mysqli']));
        });
        $app['imageboard.common'] = $app->share(function () use ($app) {
            return new Common($app, $app['imageboard.board'], $app['imageboard.thread'], $app['imageboard.post']);
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