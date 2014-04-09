<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Provider;

use Trillium\Service\Imageboard\MySQLi\Board;
use Trillium\Service\Imageboard\MySQLi\Post;
use Trillium\Service\Imageboard\MySQLi\Thread;
use Vermillion\Container;
use Vermillion\Provider\ServiceProviderInterface;

/**
 * Imageboard Class
 *
 * @package Trillium\Provider
 */
class Imageboard implements ServiceProviderInterface
{

    /**
     * {@inheritdoc}
     */
    public function registerServices(Container $container)
    {
        $container['board'] = function ($c) {
            return new Board($c['mysqli'], 'boards');
        };
        $container['thread'] = function ($c) {
            return new Thread($c['mysqli'], 'threads');
        };
        $container['post'] = function ($c) {
            return new Post($c['mysqli'], 'posts');
        };
    }

}
