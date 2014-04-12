<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Provider;

use Ciconia\Ciconia;
use Vermillion\Container;
use Vermillion\Provider\ServiceProviderInterface;

/**
 * Markdown Class
 *
 * @package Trillium\Provider
 */
class Markdown implements ServiceProviderInterface
{

    /**
     * {@inheritdoc}
     */
    public function registerServices(Container $container)
    {
        $container['markdown'] = function () {
            $parser = new Ciconia();
            $parser
                ->removeExtension('htmlBlock')
                ->removeExtension('image');

            return $parser;
        };
    }

}
