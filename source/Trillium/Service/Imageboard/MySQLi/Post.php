<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Imageboard\MySQLi;

use Trillium\Service\Imageboard\Exception\PostNotFoundException;
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
    public function get($post)
    {
        $data = parent::getItem('id', $post);
        if (!is_array($data)) {
            throw new PostNotFoundException($post);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function create($board, $thread, $message, $timestamp)
    {
        $this->mysqli->query(
            sprintf(
                "INSERT INTO `%s` SET `board` = '%s', `thread` = '%u', `message` = '%s', `time` = '%u'",
                $this->tableName,
                $this->mysqli->real_escape_string($board),
                $thread,
                $this->mysqli->real_escape_string($message),
                $timestamp
            )
        );

        return $this->mysqli->insert_id;
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
    public function listing($thread)
    {
        return parent::listingItems(
            sprintf(
                "SELECT * FROM `%s` WHERE `thread` = '%u' ORDER BY `time` ASC",
                $this->tableName,
                $thread
            )
        );
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
    public function removeThread($id)
    {
        return parent::removeItem('thread', $id);
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
