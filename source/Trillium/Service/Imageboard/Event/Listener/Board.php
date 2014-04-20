<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Imageboard\Event\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Trillium\Service\Image\Manager;
use Trillium\Service\Imageboard\Event\Event\BoardRemove;
use Trillium\Service\Imageboard\Event\Event\BoardUpdateSuccess;
use Trillium\Service\Imageboard\Event\Events;
use Trillium\Service\Imageboard\ImageInterface;
use Trillium\Service\Imageboard\PostInterface;
use Trillium\Service\Imageboard\ThreadInterface;

/**
 * Board Class
 *
 * @package Trillium\Service\Imageboard\Event\Listener
 */
class Board implements EventSubscriberInterface
{

    /**
     * @var ThreadInterface
     */
    private $thread;

    /**
     * @var PostInterface
     */
    private $post;

    /**
     * @var ImageInterface
     */
    private $image;

    /**
     * @var Manager
     */
    private $manager;

    /**
     * Constructor
     *
     * @param ThreadInterface $thread
     * @param PostInterface   $post
     * @param ImageInterface  $image
     *
     * @param Manager         $manager
     *
     * @return self
     */
    public function __construct(
        ThreadInterface $thread,
        PostInterface $post,
        ImageInterface $image,
        Manager $manager
    ) {
        $this->thread  = $thread;
        $this->post    = $post;
        $this->image   = $image;
        $this->manager = $manager;
    }

    /**
     * Moves threads and posts when board was renamed
     *
     * @param BoardUpdateSuccess $event
     *
     * @return void
     */
    public function onUpdateSuccess(BoardUpdateSuccess $event)
    {
        $newName = $event->getNewName();
        $oldName = $event->getOldName();
        if ($newName != $oldName) {
            $this->thread->move($oldName, $newName);
            $this->post->move($oldName, $newName);
            $this->image->move($oldName, $newName);
        }
    }

    /**
     * Removes threads, posts and images when a board was removed
     *
     * @param BoardRemove $event An event instance
     *
     * @return void
     */
    public function onRemove(BoardRemove $event)
    {
        $board = $event->getBoard();
        $this->thread->removeBoard($board);
        $this->post->removeBoard($board);
        $this->image->removeBoard($board);
        $this->manager->remove(
            array_map(
                function ($thread) {
                    return $thread['id'];
                },
                $this->thread->getBoard($board)
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::BOARD_REMOVE         => 'onRemove',
            Events::BOARD_UPDATE_SUCCESS => 'onUpdateSuccess',
        ];
    }

}
