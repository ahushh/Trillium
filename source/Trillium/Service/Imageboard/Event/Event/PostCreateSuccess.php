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
 * PostCreateSuccess Class
 *
 * @package Trillium\Service\Imageboard\Event\Event
 */
class PostCreateSuccess extends Event
{

    /**
     * @var int ID of created post
     */
    private $post;

    /**
     * @var Request Request instance
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
     * Constructor
     *
     * @param Request $request A request instance
     * @param string  $board   Name of parent board
     * @param int     $thread  ID of parent thread
     * @param int     $post    ID of created post
     *
     * @return self
     */
    public function __construct(Request $request, $board, $thread, $post)
    {
        $this->post    = $post;
        $this->request = $request;
        $this->board   = $board;
        $this->thread  = $thread;
    }

    /**
     * Returns ID of created post
     *
     * @return int
     */
    public function getPost()
    {
        return $this->post;
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

    /**
     * Returns thread ID
     *
     * @return int
     */
    public function getThread()
    {
        return $this->thread;
    }

}
