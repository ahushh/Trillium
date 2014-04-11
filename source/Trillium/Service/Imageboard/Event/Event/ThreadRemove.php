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
 * ThreadRemove Class
 *
 * @package Trillium\Service\Imageboard\Event\Event
 */
class ThreadRemove extends Event
{
    /**
     * @var int ID of the thread
     */
    private $thread;

    /**
     * Constructor
     *
     * @param int $thread ID of the thread
     *
     * @return self
     */
    public function __construct($thread)
    {
        $this->thread = (int) $thread;
    }

    /**
     * Returns ID of the thread
     *
     * @return int
     */
    public function getThread()
    {
        return $this->thread;
    }

}
