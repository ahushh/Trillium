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
     * @var string Name
     */
    private $name;

    /**
     * @var string Path to the stream
     */
    private $stream;

    /**
     * @var boolean Is debug
     */
    private $debug;

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
        $this->name   = $name;
        $this->stream = $stream;
        $this->debug  = $debug;
    }

    /**
     * Returns logger service
     *
     * @return Logger
     */
    public function logger()
    {
        if ($this->logger === null) {
            $this->logger = new Logger($this->name);
            $level = $this->debug ? Logger::DEBUG : Logger::ERROR;
            $this->logger->pushHandler(new StreamHandler($this->stream, $level));
        }

        return $this->logger;
    }

}
