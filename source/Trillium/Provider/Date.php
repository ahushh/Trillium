<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Provider;

use Vermillion\Container;
use Vermillion\Provider\ServiceProviderInterface;

/**
 * Date Class
 *
 * @package Trillium\Provider
 */
class Date implements ServiceProviderInterface
{

    /**
     * {@inheritdoc}
     */
    public function registerServices(Container $container)
    {
        $container['date'] = function ($c) {
            /** @var $config \Vermillion\Configuration\Configuration */
            $config = $c['configuration'];

            return new \Trillium\Service\Date\Date($config->get('settings')['timeshift']);
        };
    }

}
