<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Imageboard\Event\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * PostRemove Class
 *
 * @package Trillium\Service\Imageboard\Event\Event
 */
class PostRemove extends Event
{

    /**
     * @var int Post ID
     */
    private $post;

    /**
     * Constructor
     *
     * @param int $post Post ID
     *
     * @return self
     */
    public function __construct($post)
    {
        $this->post = $post;
    }

    /**
     * Returns post ID
     *
     * @return int
     */
    public function getPost()
    {
        return $this->post;
    }

}
