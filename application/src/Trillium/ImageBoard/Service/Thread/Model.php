<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\ImageBoard\Service\Thread;

use Trillium\Model\ModelExtended;
use Trillium\Model\MySQLi;

/**
 * Model Class
 *
 * @package Trillium\ImageBoard\Service\Thread
 */
class Model extends ModelExtended {

    /**
     * @var string Name of the posts table in database
     */
    private $postsTable;

    /**
     * Create Model instance
     *
     * @param MySQLi $mysqli     MySQLi object
     * @param string $tableName  Threads table
     * @param string $postsTable Posts table
     *
     * @return Model
     */
    public function __construct(MySQLi $mysqli, $tableName, $postsTable) {
        parent::__construct($mysqli, $tableName);
        $this->postsTable = $postsTable;
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
        return $this->save([
            'board'   => $board,
            'theme'   => $theme,
            'created' => time(),
        ]);
    }

    /**
     * Bump thread
     *
     * @param int      $tid  ID of the thread
     * @param int|null $pid  ID of the first post
     * @param boolean  $bump Update time?
     *
     * @throws \InvalidArgumentException
     * @return void
     */
    public function bump($tid, $pid = null, $bump = true) {
        if (!is_int($tid)) {
            throw new \InvalidArgumentException('Unexpected type of tid. Integer expected.');
        }
        $statement = [];
        if ($bump) {
            $statement[] = "`bump` = '" . time() . "'";
        }
        if ($pid !== null) {
            $statement[] = "`op` = '" . (int) $pid . "'";
        }
        if (!empty($statement)) {
            $this->db->query("UPDATE `" . $this->tableName . "` SET " . implode(",", $statement) . " WHERE `id` = '" . $tid . "'");
        }
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
    public function getThreads($board = null, $offset = null, $limit = null) {
        if ($board !== null) {
            $board = $this->db->real_escape_string($board);
            $where = "WHERE `" . $this->tableName . "`.`board` = '" . $board . "'";
        }
        $result = $this->db->query(
            "SELECT `" . $this->tableName . "`.*, `" . $this->postsTable . "`.`text` FROM `" . $this->tableName . "` "
            . "LEFT JOIN `" . $this->postsTable . "` ON `" . $this->tableName . "`.`op` = `" . $this->postsTable . "`.`id` "
            . (isset($where) ? $where : "")
            . " ORDER BY `bump` DESC "
            . ($offset !== null || $limit !== null ? "LIMIT " . (int) $offset . ", " . (int) $limit : "")
        );
        $list = [];
        while (($item = $result->fetch_assoc())) {
            $list[] = $item;
        }
        $result->free();
        return $list;
    }

    /**
     * Get the thread
     *
     * @param int $id ID
     *
     * @throws \InvalidArgumentException
     * @return array|null
     */
    public function get($id) {
        return $this->findItem('id', $id);
    }

    /**
     * Get number of threads in the board
     *
     * @param string $board Name of the board
     *
     * @return int
     */
    public function total($board) {
        return $this->count("`board` = '" . $this->db->real_escape_string($board) . "'");
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
        return parent::getList(
            [
                'by'        => 'bump',
                'direction' => 'ASC'
            ],
            "`board` = '" . $this->db->real_escape_string($board) . "'",
            [
                'offset' => 0,
                'limit'  => (int) $redundant
            ]
        );
    }

    /**
     * Remove thread(s)
     *
     * @param string           $key   Remove by
     * @param string|int|array $value Value
     *
     * @throws \UnexpectedValueException
     * @return void
     */
    public function remove($key, $value) {
        if ($key !== 'id' && $key !== 'board') {
            throw new \UnexpectedValueException('Unexpected value of the $by: id or board expected');
        }
        parent::remove($key, $value);
    }

} 