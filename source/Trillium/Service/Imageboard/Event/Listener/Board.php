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
     * @var ThreadInterface ThreadInterface instance
     */
    private $thread;

    /**
     * @var PostInterface PostInterface instance
     */
    private $post;

    /**
     * @var ImageInterface ImageInterfaces instance
     */
    private $image;

    /**
     * @var Manager Manager instance
     */
    private $manager;

    /**
     * Constructor
     *
     * @param ThreadInterface $thread  ThreadInterface instance
     * @param PostInterface   $post    PostInterface instance
     * @param ImageInterface  $image   ImageInterfaces instance
     * @param Manager         $manager Manager instance
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
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::BOARD_REMOVE         => 'onRemove',
            Events::BOARD_UPDATE_SUCCESS => 'onUpdateSuccess',
        ];
    }

    /**
     * Performs after a board was renamed
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
     * Performs after a board was removed
     *
     * @param BoardRemove $event An event instance
     *
     * @return void
     */
    public function onRemove(BoardRemove $event)
    {
        $board = $event->getBoard();
        $this->manager->remove(
            array_map(
                function ($thread) {
                    return $thread['id'];
                },
                $this->thread->getBoard($board)
            )
        );
        $this->thread->removeBoard($board);
        $this->post->removeBoard($board);
        $this->image->removeBoard($board);
    }

}
