<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\ImageBoard\Exception;

/**
 * ArrayMessageException Class
 *
 * Allows set exception message as array
 *
 * @package Trillium\ImageBoard\Exception
 */
class ArrayMessageException extends \Exception {

    /**
     * @var array Messages
     */
    protected $message;

    /**
     * Create ArrayMessageException instance
     *
     * @param array|string $message  Messages
     * @param int          $code     Code
     * @param \Exception   $previous Previous Exception
     *
     * @return ArrayMessageException
     */
    public function __construct($message, $code = 0, \Exception $previous = null) {
        parent::__construct(get_class($this), $code, $previous);
        $this->message = $message;
    }

}