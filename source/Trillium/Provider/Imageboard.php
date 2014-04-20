<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Provider;

use Kilte\AccountManager\Exception\AccessDeniedException;
use Trillium\Service\Imageboard\Event\Listener\Board as BoardListener;
use Trillium\Service\Imageboard\Event\Listener\Post as PostListener;
use Trillium\Service\Imageboard\Event\Listener\Thread as ThreadListener;
use Trillium\Service\Imageboard\MySQLi\Board as BoardService;
use Trillium\Service\Imageboard\MySQLi\Image as ImageService;
use Trillium\Service\Imageboard\MySQLi\Post as PostService;
use Trillium\Service\Imageboard\MySQLi\Thread as ThreadService;
use Trillium\Service\Imageboard\Validator;
use Vermillion\Container;
use Vermillion\Provider\ServiceProviderInterface;
use Vermillion\Provider\SubscriberProviderInterface;

/**
 * Imageboard Class
 *
 * @package Trillium\Provider
 */
class Imageboard implements ServiceProviderInterface, SubscriberProviderInterface
{

    /**
     * {@inheritdoc}
     */
    public function registerServices(Container $container)
    {
        $container['board']           = function ($c) {
            return new BoardService($c['mysqli'], 'boards');
        };
        $container['thread']          = function ($c) {
            return new ThreadService($c['mysqli'], 'threads');
        };
        $container['post']            = function ($c) {
            return new PostService($c['mysqli'], 'posts');
        };
        $container['image']           = function ($c) {
            return new ImageService($c['mysqli'], 'images');
        };
        $container['board.listener']  = function ($c) {
            return new BoardListener(
                $c['thread'],
                $c['post'],
                $c['image'],
                $c['imageManager']
            );
        };
        $container['thread.listener'] = function ($c) {
            return new ThreadListener(
                $c['post'],
                $c['validator'],
                $c['imageValidator'],
                $c['imageManager'],
                $c['image'],
                $this->getCaptcha($c)
            );
        };
        $container['post.listener']   = function ($c) {
            return new PostListener(
                $c['imageValidator'],
                $c['image'],
                $c['imageManager'],
                $this->getCaptcha($c)
            );
        };
        $container['validator']       = function () {
            return new Validator();
        };
    }

    /**
     * Returns the `captcha.test function` from given container
     *
     * @param array|\ArrayAccess $c container
     *
     * @return callable|null
     */
    private function getCaptcha($c)
    {
        try {
            /** @var $userController \Kilte\AccountManager\Controller\ControllerInterface */
            $userController = $c['userController'];
            $userController->getUser();
            $captcha = null;
        } catch (AccessDeniedException $e) {
            $captcha = $c['captcha.test'];
        }

        return $captcha;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribers(Container $container)
    {
        return [
            $container['board.listener'],
            $container['post.listener'],
            $container['thread.listener'],
        ];
    }

}
