<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\Exception;

/**
 * UnexpectedValueException Class
 *
 * @package Trillium\Exception
 */
class UnexpectedValueException extends \UnexpectedValueException
{
    /**
     * Create UnexpectedValueException instance
     *
     * @param string          $argName  Name of the argument
     * @param string          $expected Expected value
     * @param int             $code     The Exception code
     * @param \Exception|null $previous The previous exception used for the exception chaining
     *
     * @return UnexpectedValueException
     */
    public function __construct($argName, $expected, $code = 0, \Exception $previous = null)
    {
        $message = 'Unexpected value of the argument ' . $argName . ', ' . $expected . ' expected';
        parent::__construct($message, $code, $previous);
    }

}
