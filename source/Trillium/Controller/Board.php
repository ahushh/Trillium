<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Controller;

use Symfony\Component\HttpFoundation\Request;
use Trillium\Service\Imageboard\Event\Event\BoardRemove;
use Trillium\Service\Imageboard\Event\Events;
use Trillium\Service\Imageboard\Exception\BoardNotFoundException;

/**
 * Board Class
 *
 * @package Trillium\Controller
 */
class Board extends Controller
{

    /**
     * Creates a board
     *
     * @param Request $request
     *
     * @throws \InvalidArgumentException
     * @return array
     */
    public function create(Request $request)
    {
        $name    = $request->get('name', '');
        $summary = $request->get('summary', '');
        $error   = $this->validate($name, $summary);
        if ($this->board->isExists($name)) {
            $error[] = 'Board already exists';
        }
        if (empty($error)) {
            $this->board->create($name, $summary);
            $result = ['success' => 'Board created'];
        } else {
            $result = ['error' => $error, '_status' => 400];
        }

        return $result;
    }

    /**
     * Returns a board data
     *
     * @param string $name Name
     *
     * @return array
     */
    public function get($name)
    {
        try {
            $result = $this->board->get($name);
        } catch (BoardNotFoundException $e) {
            $result = ['error' => 'Board does not exists', '_status' => 404];
        }

        return $result;
    }

    /**
     * Updates board data
     *
     * @param Request $request
     * @param string  $board
     *
     * @throws BoardNotFoundException
     * @throws \InvalidArgumentException
     * @return array
     */
    public function update(Request $request, $board)
    {
        if (!$this->board->isExists($board)) {
            $result = ['error' => 'Board does not exists', '_status' => 404];
        } else {
            $name    = $request->get('name', '');
            $summary = $request->get('summary', '');
            $error   = $this->validate($name, $summary);
            if ($name != $board && $this->board->isExists($name)) {
                $error[] = 'Board already exists';
            }
            if (empty($error)) {
                $this->board->update($name, $summary, $board);
                $result = ['success' => 'Board updated'];
            } else {
                $result = ['error' => $error, '_status' => 400];
            }
        }

        return $result;
    }

    /**
     * Removes a board
     *
     * @param string $name Name
     *
     * @return array
     */
    public function delete($name)
    {
        $affectedRows = $this->board->delete($name);
        if ($affectedRows > 0) {
            $this->dispatcher->dispatch(Events::BOARD_REMOVE, new BoardRemove($name));
            $result = ['success' => 'Board deleted'];
        } else {
            $result = ['error' => 'Board does not exists', '_status' => 404];
        }

        return $result;
    }

    /**
     * Returns list of boards
     *
     * @return array
     */
    public function listing()
    {
        return $this->board->listing();
    }

    /**
     * Validate board data
     *
     * @param string $name    Name
     * @param string $summary Summary
     *
     * @return array
     */
    private function validate($name, $summary)
    {
        $error = [];
        if (empty($name)) {
            $error[] = 'Name could not be empty';
        } elseif (preg_match('~[^a-z0-9]~', $name)) {
            $error[] = 'Name contains disallowed characters';
        } elseif (strlen($name) > 10) {
            $error[] = 'Name length exceeds 10 characters';
        }
        if (strlen($summary) > 100) {
            $error[] = 'Summary length exceeds 100 characters';
        }

        return $error;
    }

}
