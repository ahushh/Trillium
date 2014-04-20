<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Imageboard\Exception;

/**
 * BoardNotFoundException Class
 *
 * @package Trillium\Service\Imageboard\Exception
 */
class BoardNotFoundException extends NotFoundException
{

    /**
     * {@inheritdoc}
     */
    protected function getType()
    {
        return 'Board';
    }

}
