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
class ImageNotFoundException extends NotFoundException
{

    /**
     * {@inheritdoc}
     */
    protected function getType()
    {
        return 'Image';
    }

}
