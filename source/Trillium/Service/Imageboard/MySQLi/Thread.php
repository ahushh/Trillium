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
        return parent::removeItem('id', $id);
    }

    /**
     * {@inheritdoc}
     */
    public function listing($board)
    {
        return parent::listingItems(
            sprintf(
                "SELECT * FROM `%s` WHERE `board` = '%s'",
                $this->tableName,
                $this->mysqli->real_escape_string($board)
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $thread = parent::getItem('id', $id);
        if ($thread === null) {
            throw new ThreadNotFoundException($id);
        }

        return $thread;
    }

    /**
     * {@inheritdoc}
     */
    public function isExists($id)
    {
        return parent::isItemExists('id', $id);
    }

    /**
     * {@inheritdoc}
     */
    public function removeBoard($board)
    {
        return parent::removeItem('board', $board);
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
