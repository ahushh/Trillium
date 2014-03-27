<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Provider;

use Kilte\AccountManager\Provider\MySQLiUserProvider;
use Kilte\SecurityProvider\Provider;
use Vermillion\Container;
use Vermillion\Provider\ServiceProviderInterface;
use Vermillion\Provider\SubscriberProviderInterface;

/**
 * Security Class
 *
 * @package Trillium\Provider
 */
class Security implements ServiceProviderInterface, SubscriberProviderInterface
{

    /**
     * {@inheritdoc}
     */
    public function registerServices(Container $container)
    {
        $container['security.mysqli_user_provider'] = function ($c) {
            return (new MySQLiUserProvider($c['mysqli'], 'users', 'username'))
                ->setSupportsClass('Kilte\AccountManager\User\User');
        };
        $container['security.provider']             = function ($c) {
            /**
             * @var $router \Symfony\Component\Routing\Router
             * @var $configuration \Vermillion\Configuration\Configuration
             */
            $router        = $c['router'];
            $configuration = $c['configuration'];
            $config        = $configuration->load('security');

            return new Provider(
                [
                    'http_kernel'                   => $c['http_kernel'],
                    'dispatcher'                    => $c['dispatcher'],
                    'logger'                        => $c['logger'],
                    'url_generator'                 => $router->getGenerator(),
                    'url_matcher'                   => $router->getMatcher(),
                    'http_port'                     => $config->get('http_port'),
                    'https_port'                    => $config->get('https_port'),
                    'firewalls'                     => $config->get('firewalls'),
                    'access_rules'                  => $config->get('access_rules'),
                    'hide_user_not_found'           => $config->get('hide_user_not_found'),
                    'security.mysqli_user_provider' => $c['security.mysqli_user_provider'],
                ]
            );
        };
        $container['security']                      = function ($c) {
            return $c['security.provider']['security']($c['security.provider']);
        };
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribers(Container $container)
    {
        return [
            $container['security.provider']['firewall']
        ];
    }

}
