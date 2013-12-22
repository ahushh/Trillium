<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\Exception;

/**
 * InvalidArgumentException Class
 *
 * @package Trillium\Exception
 */
class InvalidArgumentException extends \InvalidArgumentException
{
    /**
     * Construct the exception
     *
     * @param string     $argument Name of the argument
     * @param string     $expected Expected type
     * @param string     $given    Type given
     * @param int        $code     The Exception code.
     * @param \Exception $previous The previous exception used for the exception chaining
     *
     * @internal param string $message The Exception message to throw.
     * @return InvalidArgumentException
     */
    public function __construct($argument, $expected, $given, $code = 0, \Exception $previous = null)
    {
        $message = 'Expects argument ' . $argument . ' to be ' . $expected . ', ' . $given . ' given';
        parent::__construct($message, $code, $previous);
    }

}
