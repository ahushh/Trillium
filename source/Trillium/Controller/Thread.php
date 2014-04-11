<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Controller;

use Symfony\Component\HttpFoundation\Request;
use Trillium\Service\Imageboard\Event\Event\ThreadRemove;
use Trillium\Service\Imageboard\Event\Events;
use Trillium\Service\Imageboard\Exception\ThreadNotFoundException;

/**
 * Thread Class
 *
 * @package Trillium\Controller
 */
class Thread extends Controller
{

    /**
     * Creates a thread
     *
     * @param Request $request A request instance
     *
     * @throws \InvalidArgumentException
     * @return array
     */
    public function create(Request $request)
    {
        $title   = $request->get('title', '');
        $board   = $request->get('board', '');
        $message = $request->get('message', '');
        $error   = $this->validator->thread($title, $message);
        if (!$this->board->isExists($board)) {
            $error[] = 'Board does not exists';
        }
        if (!empty($error)) {
            $result = ['error' => $error, '_status' => 400];
        } else {
            $thread = $this->thread->create($title, $board);
            $this->post->create($board, $thread, $message);
            $result = ['success' => $thread];
        }

        return $result;
    }

    /**
     * Returns thread data
     *
     * @param int $id ID of thread
     *
     * @return array
     */
    public function get($id)
    {
        try {
            $result = $this->thread->get($id);
        } catch (ThreadNotFoundException $e) {
            $result = ['error' => $e->getMessage(), '_status' => 404];
        }

        return $result;
    }

    /**
     * Returns list of threads for the given board
     *
     * @param string $board Board name
     *
     * @return array
     */
    public function listing($board)
    {
        if (!$this->board->isExists($board)) {
            $result = ['error' => 'Board does not exists', '_status' => 404];
        } else {
            $result = $this->thread->listing($board);
        }

        return $result;
    }

    /**
     * Renames a thread
     *
     * @param Request $request A request instance
     * @param int     $id      Thread id
     *
     * @return array
     */
    public function rename(Request $request, $id)
    {
        if (!$this->thread->isExists($id)) {
            $result = ['error' => 'Thread does not exists', '_status' => 404];
        } else {
            $title = $request->get('title', '');
            $error = $this->validator->threadTitle($title);
            if (!empty($error)) {
                $result = ['error' => $error, '_status' => 400];
            } else {
                $this->thread->rename($id, $title);
                $result = ['success' => 'Thread renamed'];
            }
        }

        return $result;
    }

    /**
     * Removes a thread
     *
     * @param int $id Thread ID
     *
     * @return array
     */
    public function remove($id)
    {
        if ($this->thread->remove($id) > 0) {
            $this->dispatcher->dispatch(Events::THREAD_REMOVE, new ThreadRemove($id));
            $result = ['success' => 'Thread removed'];
        } else {
            $result = ['error' => 'Thread does not exists', '_status' => 404];
        }

        return $result;
    }

}
