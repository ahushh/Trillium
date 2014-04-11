<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Imageboard\MySQLi;

use Trillium\Service\Imageboard\PostInterface;

/**
 * Post Class
 *
 * @package Trillium\Service\Imageboard\MySQLi
 */
class Post extends MySQLi implements PostInterface
{

    /**
     * {@inheritdoc}
     */
    public function create($thread, $message)
    {
        $this->mysqli->query(
            sprintf(
                "INSERT INTO `%s` SET `thread` = '%s', `message` = '%s'",
                $this->tableName,
                (int) $thread,
                $this->mysqli->real_escape_string($message)
            )
        );

        return $this->mysqli->insert_id;
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
    public function removeThread($id)
    {
        $this->mysqli->query(
            sprintf(
                "DELETE FROM `%s` WHERE `thread` = '%s'",
                $this->tableName,
                (int) $id
            )
        );

        return $this->mysqli->affected_rows;
    }

}
