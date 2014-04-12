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
 * ThreadCreateBefore Class
 *
 * @package Trillium\Service\Imageboard\Event\Event
 */
class ThreadCreateBefore extends Event
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
     * @var array Occurred errors
     */
    private $error;

    /**
     * Constructor
     *
     * @param Request $request A request instance
     * @param string  $board   Board name
     *
     * @return self
     */
    public function __construct(Request $request, $board)
    {
        $this->request = $request;
        $this->board   = $board;
        $this->error   = [];
    }

    /**
     * Returns a requests
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
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
     * Returns errors
     *
     * @return array
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Merge errors
     *
     * @param array $error
     *
     * @return $this
     */
    public function setError($error)
    {
        $this->error = array_merge($this->error, $error);

        return $this;
    }

}
