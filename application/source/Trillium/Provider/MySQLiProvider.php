<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Provider;

use Trillium\Service\MySQLi\MySQLi;

/**
 * MySQLiProvider Class
 *
 * @package Trillium\Provider
 */
class MySQLiProvider
{

    /**
     * @var MySQLi MySQLi instance
     */
    private $mysqli;

    /**
     * Constructor
     *
     * @param array $configuration Connection parameters
     *
     * @return self
     */
    public function __construct(array $configuration = [])
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

        foreach ($defaults as $key => $value) {
            if (!array_key_exists($key, $configuration)) {
                $configuration[$key] = $value;
            }
        }
        $this->mysqli = new MySQLi($configuration);
        $this->mysqli->set_charset($configuration['charset']);
    }

    /**
     * Returns the MySQLi instance
     *
     * @return MySQLi
     */
    public function mysqli()
    {
        return $this->mysqli;
    }

}
