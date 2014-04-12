<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Imageboard\Event\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

/**
 * ThreadCreateSuccess Class
 *
 * @package Trillium\Service\Imageboard\Event\Event
 */
class ThreadCreateSuccess extends Event
{

    /**
     * @var int Thread ID
     */
    private $thread;

    /**
     * @var string Board name
     */
    private $board;

    /**
     * @var Request A request instance
     */
    private $request;

    /**
     * Constructor
     *
     * @param int     $thread  Thread ID
     * @param string  $board   Board name
     * @param Request $request A request instance
     *
     * @return self
     */
    public function __construct($thread, $board, Request $request)
    {
        $this->thread  = $thread;
        $this->board   = $board;
        $this->request = $request;
    }

    /**
     * Returns thread ID
     *
     * @return int
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * Returns board name
     *
     * @return string
     */
    public function getBoard()
    {
        return $this->board;
    }

    /**
     * Returns a request instance
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

}
