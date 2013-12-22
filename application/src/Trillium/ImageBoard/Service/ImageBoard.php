<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

namespace Trillium\ImageBoard\Service;

use Trillium\Exception\InvalidArgumentException;
use Trillium\ImageBoard\Service\Board\Board;
use Trillium\ImageBoard\Service\Image\Image;
use Trillium\ImageBoard\Service\Post\Post;
use Trillium\ImageBoard\Service\Thread\Thread;

/**
 * ImageBoard Class
 *
 * @package Trillium\ImageBoard\Service
 */
class ImageBoard
{
    /**
     * @var Board Board service
     */
    private $board;

    /**
     * @var Thread Thread service
     */
    private $thread;

    /**
     * @var Post Post service
     */
    private $post;

    /**
     * @var Image Image service
     */
    private $image;

    /**
     * @var Markup Markup service
     */
    private $markup;

    /**
     * @var string Path to the resources directory
     */
    private $resourcesDir;

    /**
     * Create ImageBoard instance
     *
     * @param Board  $board        Board service
     * @param Thread $thread       Thread service
     * @param Post   $post         Post service
     * @param Image  $image        Image service
     * @param Markup $markup       Markup service
     * @param string $resourcesDir Path to the resources directory
     *
     * @throws \RuntimeException
     * @return ImageBoard
     */
    public function __construct(Board $board, Thread $thread, Post $post, Image $image, Markup $markup, $resourcesDir)
    {
        $this->board  = $board;
        $this->thread = $thread;
        $this->post   = $post;
        $this->image  = $image;
        $this->markup = $markup;
        $resourcesDir = realpath($resourcesDir);
        if ($resourcesDir === false) {
            throw new \RuntimeException('Directory ' . $resourcesDir . ' is not exists.');
        }
        $this->resourcesDir = $resourcesDir . DS;
    }

    /**
     * Returns Board service
     *
     * @return Board
     */
    public function board()
    {
        return $this->board;
    }

    /**
     * Returns Thread service
     *
     * @return Thread
     */
    public function thread()
    {
        return $this->thread;
    }

    /**
     * Returns Post service
     *
     * @return Post
     */
    public function post()
    {
        return $this->post;
    }

    /**
     * Returns Image service
     *
     * @return Image
     */
    public function image()
    {
        return $this->image;
    }

    /**
     * Returns Markup service
     *
     * @return Markup
     */
    public function markup()
    {
        return $this->markup;
    }

    /**
     * Remove all data of the board
     *
     * @param string $name Name of the board
     *
     * @return void
     */
    public function removeBoard($name)
    {
        $this->board()->remove($name);
        $this->thread()->remove($name, Thread::BOARD);
        $this->post()->remove($name, Post::BOARD);
        $this->image()->remove($name, Image::BOARD);
    }

    /**
     * Remove thread
     * You could pass array for remove list
     *
     * @param int|array $id ID
     *
     * @return void
     * @throws InvalidArgumentException
     */
    public function removeThread($id)
    {
        if (!is_int($id) && !is_array($id)) {
            throw new InvalidArgumentException('id', 'integer, array', gettype($id));
        }
        if (is_array($id)) {
            $id = array_map('intval', $id);
        }
        $this->thread()->remove($id, Thread::ID);
        $this->post()->remove($id, Post::THREAD);
        $this->image()->removeFiles($this->image()->getList($id, Image::THREAD));
        $this->image()->remove($id, Image::THREAD);
    }

    /**
     * Remove post
     * You could pass array for remove list
     *
     * @param array|int $id ID
     *
     * @throws InvalidArgumentException
     * @return void
     */
    public function removePost($id)
    {
        if (!is_int($id) && !is_array($id)) {
            throw new InvalidArgumentException('id', 'integer, array', gettype($id));
        }
        if (is_array($id)) {
            $id = array_map('intval', $id);
        }
        $this->post()->remove($id, Post::ID);
        $this->image()->removeFiles($this->image()->getList($id, Image::POST));
        $this->image()->remove($id, Image::POST);
    }

    /**
     * Remove redundant threads
     *
     * @param string $board Name of the board
     * @param int    $max   The maximum number of threads
     *
     * @return void
     */
    public function removeRedundantThreads($board, $max)
    {
        $totalThreads = $this->thread()->total($board);
        $redundantThreads = $totalThreads - $max;
        if ($redundantThreads > 0) {
            $redundantThreads = $this->thread->getRedundant($board, $redundantThreads);
            if (!empty($redundantThreads)) {
                $this->post()->remove($redundantThreads, Post::THREAD);
                $this->image()->removeFiles($this->image()->getList($redundantThreads, Image::THREAD));
                $this->image()->remove($redundantThreads, Image::THREAD);
                $this->thread()->remove($redundantThreads, Thread::ID);
            }
        }
    }

}
