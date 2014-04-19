<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Imageboard\Exception;

/**
 * ImageNotFoundException Class
 *
 * @package Trillium\Service\Imageboard\Exception
 */
class ImageNotFoundException extends Exception
{

    /**
     * {@inheritdoc}
     */
    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        $message = sprintf('Image "%s" does not exists', $message);
        parent::__construct($message, $code, $previous);
    }

}
