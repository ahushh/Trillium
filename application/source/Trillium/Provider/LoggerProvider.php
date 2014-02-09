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
use Trillium\General\Application;

/**
 * LoggerProvider Class
 *
 * @package Trillium\Provider
 */
class LoggerProvider
{

    /**
     * Creates the logger instance
     *
     * @param Application $app An application instance
     *
     * @return Logger
     */
    public function register(Application $app)
    {
        $logger = new Logger('Trillium');
        $stream = $app->getLogsDir() . $app->getEnvironment() . '.log';
        $level  = $app->isDebug() ? Logger::DEBUG : Logger::ERROR;
        $logger->pushHandler(new StreamHandler($stream, $level));

        return $logger;
    }

}
