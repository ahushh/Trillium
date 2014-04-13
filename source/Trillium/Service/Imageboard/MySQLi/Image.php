<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Imageboard\MySQLi;

use Trillium\Service\Imageboard\ImageInterface;

/**
 * Image Class
 *
 * @package Trillium\Service\Imageboard\MySQLi
 */
class Image extends MySQLi implements ImageInterface
{

    /**
     * {@inheritdoc}
     */
    public function create($board, $thread, $post, $ext)
    {
        $this->mysqli->query(
            sprintf(
                "INSERT INTO `%s` SET `board` = '%s', `thread` = '%u', `post` = '%u', `ext` = '%s'",
                $this->tableName,
                $this->mysqli->real_escape_string($board),
                $thread,
                $post,
                $this->mysqli->real_escape_string($ext)
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function get($post)
    {
        return parent::getItem('post', $post);
    }

    /**
     * {@inheritdoc}
     */
    public function getThread($thread)
    {
        return parent::listingItems(
            sprintf(
                "SELECT * FROM `%s` WHERE `thread` = '%u'",
                $this->tableName,
                $thread
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function remove($post)
    {
        return parent::removeItem('post', $post);
    }

    /**
     * {@inheritdoc}
     */
    public function removeThread($thread)
    {
        return parent::removeItem('thread', $thread);
    }

    /**
     * {@inheritdoc}
     */
    public function removeBoard($board)
    {
        return parent::removeItem('board', $board);
    }

}
