<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Imageboard\Exception;

/**
 * PostNotFoundException Class
 *
 * @package Trillium\Service\Imageboard\Exception
 */
class PostNotFoundException extends NotFoundException
{

    /**
     * Returns the type of entity
     *
     * @return string
     */
    protected function getType()
    {
        return 'Post';
    }

}
