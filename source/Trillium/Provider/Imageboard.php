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
use Trillium\Service\Imageboard\Event\Listener\Thread as ThreadListener;
use Trillium\Service\Imageboard\MySQLi\Board;
use Trillium\Service\Imageboard\MySQLi\Image;
use Trillium\Service\Imageboard\MySQLi\Post;
use Trillium\Service\Imageboard\MySQLi\Thread;
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
            return new Board($c['mysqli'], 'boards');
        };
        $container['thread']          = function ($c) {
            return new Thread($c['mysqli'], 'threads');
        };
        $container['post']            = function ($c) {
            return new Post($c['mysqli'], 'posts');
        };
        $container['image']           = function ($c) {
            return new Image($c['mysqli'], 'images');
        };
        $container['board.listener']  = function ($c) {
            return new BoardListener($c['thread'], $c['post']);
        };
        $container['thread.listener'] = function ($c) {
            return new ThreadListener($c['post'], $c['validator'], $this->getCaptcha($c));
        };
        $container['validator']       = function () {
            return new Validator();
        };
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribers(Container $container)
    {
        return [
            $container['board.listener'],
            $container['thread.listener'],
        ];
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

}
