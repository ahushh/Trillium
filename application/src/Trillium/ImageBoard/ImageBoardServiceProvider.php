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
use Trillium\ImageBoard\Service\Board\Board;
use Trillium\ImageBoard\Service\Board\Model as BoardModel;
use Trillium\ImageBoard\Service\Image\Image;
use Trillium\ImageBoard\Service\Image\Model as ImageModel;
use Trillium\ImageBoard\Service\ImageBoard;
use Trillium\ImageBoard\Service\Markup;
use Trillium\ImageBoard\Service\Message;
use Trillium\ImageBoard\Service\Post\Model as PostModel;
use Trillium\ImageBoard\Service\Post\Post;
use Trillium\ImageBoard\Service\Thread\Model as ThreadModel;
use Trillium\ImageBoard\Service\Thread\Thread;

/**
 * ImageBoardServiceProvider Class
 *
 * @package Trillium\ImageBoard
 */
class ImageBoardServiceProvider implements ServiceProviderInterface
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
        $app['imageboard.resources_path'] = null;
        $app['imageboard'] = $app->share(function () use ($app) {

            return new ImageBoard(
                new Board(new BoardModel($app['mysqli'], 'boards'), $app['imageboard.resources_path']),
                new Thread(new ThreadModel($app['mysqli'], 'threads', 'posts')),
                new Post(new PostModel($app['mysqli'], 'posts')),
                new Image(new ImageModel($app['mysqli'], 'images'), $app['imageboard.resources_path']),
                new Markup(new Highlighter(new HtmlManual())),
                $app['imageboard.resources_path']
            );
        });
        $app['imageboard.message'] = $app->share(function () use ($app) {

            return new Message($app['imageboard'], $app['captcha']);
        });
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
