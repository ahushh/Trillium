<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Provider;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * LoggerProvider Class
 *
 * @package Trillium\Provider
 */
class LoggerProvider
{

    /**
     * @var Logger Logger service
     */
    private $logger;

    /**
     * Constructor
     *
     * @param string  $name   Name
     * @param string  $stream Path to the stream
     * @param boolean $debug  Is debug
     *
     * @return self
     */
    public function __construct($name, $stream, $debug)
    {
        $this->logger = new Logger($name);
        $this->logger->pushHandler(new StreamHandler($stream, $debug ? Logger::DEBUG : Logger::ERROR));
    }

    /**
     * Returns logger service
     *
     * @return Logger
     */
    public function logger()
    {
        return $this->logger;
    }

}
