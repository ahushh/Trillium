<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Imageboard\MySQLi;

use Trillium\Service\Imageboard\Exception\ImageNotFoundException;
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
    public function create($board, $thread, $post, $ext, $width, $height, $size)
    {
        $statement = "INSERT INTO `%s` SET `board` = '%s', `thread` = '%u', "
            . "`post` = '%u', `ext` = '%s', width='%u', `height` = '%u', `size` = '%u'";
        $this->mysqli->query(
            sprintf(
                $statement,
                $this->tableName,
                $this->mysqli->real_escape_string($board),
                $thread,
                $post,
                $this->mysqli->real_escape_string($ext),
                $width,
                $height,
                $size
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function get($post)
    {
        $image = parent::getItem('post', $post);
        if (!is_array($image)) {
            throw new ImageNotFoundException($post);
        }

        return $image;
    }

    /**
     * {@inheritdoc}
     */
    public function getBoard($board)
    {
        return parent::listingItems(
            sprintf(
                "SELECT * FROM `%s` WHERE `board` = '%s'",
                $this->tableName,
                $board
            )
        );
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
