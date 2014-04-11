<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Provider;

use Trillium\Service\Imageboard\Event\Listener\Board as BoardListener;
use Trillium\Service\Imageboard\Event\Listener\Thread as ThreadListener;
use Trillium\Service\Imageboard\MySQLi\Board;
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
        $container['board.listener']  = function ($c) {
            return new BoardListener($c['thread'], $c['post']);
        };
        $container['thread.listener'] = function ($c) {
            return new ThreadListener($c['post']);
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

}
