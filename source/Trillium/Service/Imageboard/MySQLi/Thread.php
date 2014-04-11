<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Imageboard\MySQLi;

use Trillium\Service\Imageboard\Exception\ThreadNotFoundException;
use Trillium\Service\Imageboard\ThreadInterface;

/**
 * Thread Class
 *
 * @package Trillium\Service\Imageboard\MySQLi
 */
class Thread extends MySQLi implements ThreadInterface
{

    /**
     * {@inheritdoc}
     */
    public function create($title, $board)
    {
        $this->mysqli->query(
            sprintf(
                "INSERT INTO `%s` SET `title` = '%s', `board` = '%s'",
                $this->tableName,
                $this->mysqli->real_escape_string($title),
                $this->mysqli->real_escape_string($board)
            )
        );

        return $this->mysqli->insert_id;
    }

    /**
     * {@inheritdoc}
     */
    public function rename($id, $title)
    {
        $this->mysqli->query(
            sprintf(
                "UPDATE `%s` SET `title` = '%s' WHERE `id` = '%u'",
                $this->tableName,
                $this->mysqli->real_escape_string($title),
                $id
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function remove($id)
    {
        $this->mysqli->query(
            sprintf(
                "DELETE FROM `%s` WHERE `id` = '%u'",
                $this->tableName,
                $id
            )
        );

        return $this->mysqli->affected_rows;
    }

    /**
     * {@inheritdoc}
     */
    public function listing($board)
    {
        $result = $this->mysqli->query(
            sprintf(
                "SELECT * FROM `%s` WHERE `board` = '%s'",
                $this->tableName,
                $this->mysqli->real_escape_string($board)
            )
        );
        $list   = [];
        while (($thread = $result->fetch_assoc())) {
            $list[] = $thread;
        }
        $result->free();

        return $list;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $result = $this->mysqli->query(
            sprintf(
                "SELECT * FROM `%s` WHERE `id` = '%u'",
                $this->tableName,
                $id
            )
        );
        $thread = $result->fetch_assoc();
        $result->free();
        if (!is_array($thread)) {
            throw new ThreadNotFoundException($id);
        }

        return $thread;
    }

    /**
     * {@inheritdoc}
     */
    public function isExists($id)
    {
        $result = $this->mysqli->query(
            sprintf(
                "SELECT COUNT(*) FROM `%s` WHERE `id` = '%u'",
                $this->tableName,
                $id
            )
        );
        $total  = (int) $result->fetch_row()[0];
        $result->free();

        return $total > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function removeBoard($board)
    {
        $this->mysqli->query(
            sprintf(
                "DELETE FROM `%s` WHERE `board` = '%s'",
                $this->tableName,
                $this->mysqli->real_escape_string($board)
            )
        );

        return $this->mysqli->affected_rows;
    }

    /**
     * {@inheritdoc}
     */
    public function move($old, $new)
    {
        $this->mysqli->query(
            sprintf(
                "UPDATE `%s` SET `board` = '%s' WHERE `board` = '%s'",
                $this->tableName,
                $this->mysqli->real_escape_string($new),
                $this->mysqli->real_escape_string($old)
            )
        );
    }

}
