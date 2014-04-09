<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Imageboard\MySQLi;

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
                "UPDATE `%s` SET `title` = '%s' WHERE `id` = '%s'",
                $this->tableName,
                $this->mysqli->real_escape_string($title),
                (int) $id
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
                "DELETE FROM `%s` WHERE `id` = '%s'",
                $this->tableName,
                (int) $id
            )
        );

        return $this->mysqli->affected_rows;
    }

}
