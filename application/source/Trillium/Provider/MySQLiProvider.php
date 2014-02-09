<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Provider;

use Trillium\General\Application;
use Trillium\Service\MySQLi\MySQLi;

/**
 * MySQLiProvider Class
 *
 * @package Trillium\Provider
 */
class MySQLiProvider
{

    /**
     * Creates the MySQLi instance
     *
     * @param Application $app An application instance
     *
     * @return MySQLi
     */
    public function register(Application $app)
    {
        $defaults = [
            'host'    => ini_get("mysqli.default_host"),
            'user'    => ini_get("mysqli.default_user"),
            'pass'    => ini_get("mysqli.default_pw"),
            'db'      => '',
            'port'    => ini_get("mysqli.default_port"),
            'socket'  => ini_get("mysqli.default_socket"),
            'charset' => 'utf8',
        ];
        $configuration = $app->configuration->load('mysqli', 'yml')->get();
        foreach ($defaults as $key => $value) {
            if (!array_key_exists($key, $configuration)) {
                $configuration[$key] = $value;
            }
        }
        $mysqli = new MySQLi($configuration);
        $mysqli->set_charset($configuration['charset']);

        return $mysqli;
    }

}
