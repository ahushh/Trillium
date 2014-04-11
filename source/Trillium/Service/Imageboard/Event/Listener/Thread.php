<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Imageboard\Event\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Trillium\Service\Imageboard\Event\Event\ThreadRemove;
use Trillium\Service\Imageboard\Event\Events;
use Trillium\Service\Imageboard\PostInterface;

/**
 * Thread Class
 *
 * @package Trillium\Service\Imageboard\Event\Listener
 */
class Thread implements EventSubscriberInterface
{

    /**
     * @var PostInterface
     */
    private $post;

    /**
     * Constructor
     *
     * @param PostInterface $post
     *
     * @return self
     */
    public function __construct(PostInterface $post)
    {
        $this->post = $post;
    }

    /**
     * Removes posts when a thread was removed
     *
     * @param ThreadRemove $event
     *
     * @return void
     */
    public function onRemove(ThreadRemove $event)
    {
        $this->post->removeThread($event->getThread());
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::THREAD_REMOVE => 'onRemove',
        ];
    }

}
