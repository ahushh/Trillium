<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\ImageBoard\Service\Thread;

use Trillium\Exception\UnexpectedValueException;

/**
 * Thread Class
 *
 * @package Trillium\ImageBoard\Service\Thread
 */
class Thread {

    /**
     * Name of the ID key
     */
    const ID = 'id';

    /**
     * Name of the board key
     */
    const BOARD = 'board';

    /**
     * @var Model Model
     */
    private $model;

    /**
     * Create Thread instance
     *
     * @param Model $model Model
     *
     * @return Thread
     */
    public function __construct(Model $model) {
        $this->model = $model;
    }

    /**
     * Create the thread
     *
     * @param string $board Name of the board
     * @param string $theme Theme of the thread
     *
     * @return int
     */
    public function create($board, $theme) {
        return $this->model->create($board, $theme);
    }

    /**
     * Bump thread
     *
     * @param int      $tid  ID of the thread
     * @param int|null $pid  ID of the first post
     * @param boolean  $bump Update time?
     *
     * @return void
     */
    public function bump($tid, $pid = null, $bump = true) {
        $this->model->bump($tid, $pid, $bump);
    }

    /**
     * Get list of the threads
     *
     * @param string|null $board  Name of the board
     * @param int|null    $offset Offset
     * @param int|null    $limit  Limit
     *
     * @return array
     */
    public function getList($board = null, $offset = null, $limit = null) {
        return $this->model->getThreads($board, $offset, $limit);
    }

    /**
     * Get the thread
     *
     * @param int $id ID
     *
     * @return array|null
     */
    public function get($id) {
        return $this->model->get($id);
    }

    /**
     * Get number of threads in the board
     *
     * @param string $board Name of the board
     *
     * @return int
     */
    public function total($board) {
        return $this->model->total($board);
    }

    /**
     * Get IDs of redundant threads
     *
     * @param string $board    Name of the board
     * @param int    $redundant Redudant
     *
     * @return array
     */
    public function getRedundant($board, $redundant) {
        return array_map(
            function ($item) {
                return (int) $item['id'];
            },
            $this->model->getRedundant($board, $redundant)
        );
    }

    /**
     * Remove thread(s)
     *
     * @param string|int|array $id ID(s)
     * @param string           $by Remove by
     *
     * @throws UnexpectedValueException
     * @return void
     */
    public function remove($id, $by) {
        if ($by !== self::ID && $by !== self::BOARD) {
            throw new UnexpectedValueException('by', self::ID . ',' . self::BOARD);
        }
        $this->model->remove($by, $id);
    }

    /**
     * Update data of the thread
     *
     * @param array      $data  New data
     * @param string     $key   Update by
     * @param int|string $value Value of the key
     *
     * @return void
     */
    public function update(array $data, $key, $value) {
        $this->model->update($data, $key, $value);
    }

    /**
     * Check theme of the thread
     *
     * @param string $theme Theme
     *
     * @return array|null|string
     */
    public function checkTheme($theme) {
        if (empty($theme)) {
            $error = 'The value could not be empty';
        } elseif (strlen($theme) > 200) {
            $error = ['The length of the value must not exceed %s characters', 200];
        } else {
            $error = null;
        }
        return $error;
    }

    /**
     * Manage thread
     *
     * @param array  $thread Data of the thread
     * @param string $action Name of the action
     *
     * @return void
     * @throws UnexpectedValueException
     */
    public function manage(array $thread, $action) {
        $actions = [
            'autosage' => ['auto_sage_bump' => $thread['auto_sage_bump'] == 1 ? 0 : 1], // Autosage (Disable bump)
            'autobump' => ['auto_sage_bump' => $thread['auto_sage_bump'] == 2 ? 0 : 2], // Autobump (Disable sage)
            'attach'   => ['attach' => $thread['attach'] == 1 ? 0 : 1],                 // Attach (Never redundant)
            'close'    => ['close' => $thread['close'] == 1 ? 0 : 1],                   // Open/close
        ];
        $expected = array_keys($actions);
        if (!in_array($action, $expected)) {
            throw new UnexpectedValueException('action', implode(', ', $expected));
        }
        $this->update($actions[$action], 'id', (int) $thread['id']);
    }

}