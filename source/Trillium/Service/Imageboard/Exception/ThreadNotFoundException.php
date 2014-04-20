<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Imageboard\Exception;

/**
 * ThreadNotFoundException Class
 *
 * @package Trillium\Service\Imageboard\Exception
 */
class ThreadNotFoundException extends NotFoundException
{

    /**
     * {@inheritdoc}
     */
    protected function getType()
    {
        return 'Thread';
    }

}
