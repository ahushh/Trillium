<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Provider;

use Trillium\Service\Settings\RequestListener;
use Trillium\Service\Settings\Settings as SettingsService;
use Vermillion\Container;
use Vermillion\Provider\ServiceProviderInterface;
use Vermillion\Provider\SubscriberProviderInterface;

/**
 * Settings Class
 *
 * @package Trillium\Provider
 */
class Settings implements ServiceProviderInterface, SubscriberProviderInterface
{

    /**
     * {@inheritdoc}
     */
    public function registerServices(Container $container)
    {
        $container['settings']                  = function ($c) {
            /** @var $configuration \Vermillion\Configuration\Configuration */
            $configuration = $c['configuration'];
            $settings      = $configuration->load('settings')->get();

            return new SettingsService($configuration->get('available_skins'), $settings);
        };
        $container['settings.request_listener'] = function ($c) {
            return new RequestListener($c['settings']);
        };
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribers(Container $container)
    {
        return [$container['settings.request_listener']];
    }

}
