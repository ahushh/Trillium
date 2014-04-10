<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Controller;

use Symfony\Component\HttpFoundation\Request;

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
        $title = $request->get('title', '');
        $board = $request->get('board', '');
        $message = $request->get('message', '');
        $error = $this->validate($title, $board, $message);
        if (!empty($error)) {
            $result = ['error' => $error, '_status' => 400];
        } else {
            $thread = $this->thread->create($title, $board);
            $this->post->create($thread, $message);
            $result = ['success' => $thread];
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
     * Validates thread data
     *
     * @param string $title   Thread title
     * @param string $board   Parent board
     * @param string $message Message
     *
     * @return array
     */
    private function validate($title, $board, $message)
    {
        $error = [];
        $titleLen = strlen($title);
        $messageLen = mb_strlen($message);
        if ($titleLen < 2 || $titleLen > 30) {
            $error[] = 'Wrong thread title len';
        }
        if (!$this->board->isExists($board)) {
            $error[] = 'Board does not exists';
        }
        if ($messageLen < 2 || $messageLen > 10000) {
            $error[] = 'Wrong message len';
        }

        return $error;
    }

}
