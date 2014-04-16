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
 * PostCreateBefore Class
 *
 * @package Trillium\Service\Imageboard\Event\Event
 */
class PostCreateBefore extends Event
{

    /**
     * @var Request A request instance
     */
    private $request;

    /**
     * @var string Board name
     */
    private $board;

    /**
     * @var int Thread ID
     */
    private $thread;

    /**
     * @var array Occurred errors
     */
    private $error;

    /**
     * Constructor
     *
     * @param Request $request A request instance
     * @param string  $board   Board name
     * @param int     $thread  Thread ID
     */
    public function __construct(Request $request, $board, $thread)
    {
        $this->request = $request;
        $this->board   = $board;
        $this->thread  = $thread;
        $this->error   = [];
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

    /**
     * Returns a board name
     *
     * @return string
     */
    public function getBoard()
    {
        return $this->board;
    }

    /**
     * Returns a thread ID
     *
     * @return int
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * Returns occurred errors
     *
     * @return array
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Sets errors
     *
     * @param array $error Errors
     *
     * @return $this
     */
    public function setError(array $error)
    {
        $this->error = $error;

        return $this;
    }

}
