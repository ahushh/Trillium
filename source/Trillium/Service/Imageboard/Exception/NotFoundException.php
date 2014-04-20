<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Imageboard\Exception;

/**
 * NotFoundException Class
 *
 * @package Trillium\Service\Imageboard\Exception
 */
abstract class NotFoundException extends Exception
{

    /**
     * {@inheritdoc}
     */
    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        $message = sprintf('%s "%s" does not exists', $this->getType(), $message);
        parent::__construct($message, $code, $previous);
    }

    /**
     * Returns the type of entity
     *
     * @return string
     */
    protected abstract function getType();

} 