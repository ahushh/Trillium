<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Provider;

use Kilte\AccountManager\Controller\Controller;
use Kilte\AccountManager\EventListener\ActivityListener;
use Kilte\AccountManager\Provider\MySQLiUserProvider;
use Kilte\SecurityProvider\Provider;
use Trillium\Service\Security\AuthenticationSuccessHandler;
use Trillium\Service\Security\Controller as ControllerListener;
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
        $container['security.user_class']           = 'Kilte\AccountManager\User\User';
        $container['security.mysqli_user_provider'] = function ($c) {
            return (new MySQLiUserProvider($c['mysqli'], 'users', 'username'))
                ->setSupportsClass($c['security.user_class']);
        };
        $container['security.provider']             = function ($c) {
            /**
             * @var $router        \Symfony\Component\Routing\Router
             * @var $configuration \Vermillion\Configuration\Configuration
             */
            $router        = $c['router'];
            $configuration = $c['configuration'];
            $config        = $configuration->load('security');
            $provider      = new Provider(
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
                    'role_hierarchy'                => $config->get('role_hierarchy'),
                    'hide_user_not_found'           => $config->get('hide_user_not_found'),
                    'security.mysqli_user_provider' => $c['security.mysqli_user_provider'],
                ]
            );
            // Override authentication success handler
            $provider['authentication.success_handler._proto'] = $provider->protect(
                function ($name, $options) use ($provider, $c) {
                    return function () use ($name, $options, $provider, $c) {
                        $handler = new AuthenticationSuccessHandler(
                            $provider['http_utils'],
                            $options
                        );
                        $handler->setProviderKey($name);
                        $handler->setDateFormatter([$c['date'], 'format']);

                        return $handler;
                    };
                }
            );

            return $provider;
        };
        $container['security']                      = function ($c) {
            return $c['security.provider']['security'];
        };
        $container['userController']                = function ($c) {
            return new Controller(
                $c['security'],
                $c['security.provider']['encoder_factory'],
                $c['security.mysqli_user_provider'],
                $c['dispatcher'],
                $c['security.user_class']
            );
        };
        $container['security.controller_listener']  = function ($c) {
            /** @var $configuration \Vermillion\Configuration\Configuration */
            $configuration = $c['configuration'];
            $config        = $configuration->load('security')->get();

            return new ControllerListener($c['userController'], $c['dispatcher'], $config);
        };
        $container['security.activity_listener']    = function ($c) {
            /** @var $configuration \Vermillion\Configuration\Configuration */
            $configuration   = $c['configuration'];
            $config          = $configuration->load('security')->get('activity_listener');
            $config['delay'] = isset($config['delay']) ? (int) $config['delay'] : 300;

            return new ActivityListener(
                $c['security'],
                $c['security.mysqli_user_provider'],
                time() - $config['delay']
            );
        };
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribers(Container $container)
    {
        return [
            $container['security.provider']['firewall'],
            $container['security.provider']['remember_me.response_listener'],
            $container['security.controller_listener'],
            $container['security.activity_listener'],
        ];
    }

}
