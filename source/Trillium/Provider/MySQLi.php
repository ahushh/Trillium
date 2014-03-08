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
 * MySQLi Class
 *
 * @package Trillium\Provider
 */
class MySQLi implements ServiceProviderInterface
{

    /**
     * {@inheritdoc}
     */
    public function registerServices(Container $container)
    {
        $container['mysqli'] = function ($container) {
            $defaults = [
                'host'    => ini_get("mysqli.default_host"),
                'user'    => ini_get("mysqli.default_user"),
                'pass'    => ini_get("mysqli.default_pw"),
                'db'      => '',
                'port'    => ini_get("mysqli.default_port"),
                'socket'  => ini_get("mysqli.default_socket"),
                'charset' => 'utf8',
            ];
            /** @var $config \Vermillion\Configuration\Configuration */
            $config = $container['configuration'];
            $configuration = $config->load('mysqli')->get();
            foreach ($defaults as $key => $value) {
                if (!array_key_exists($key, $configuration)) {
                    $configuration[$key] = $value;
                }
            }
            $mysqli = new \Trillium\Service\MySQLi\MySQLi($configuration);
            $mysqli->set_charset($configuration['charset']);

            return $mysqli;
        };
    }

}
