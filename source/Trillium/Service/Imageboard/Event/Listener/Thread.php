<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Imageboard\Event\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Trillium\Service\Imageboard\Event\Event\ThreadCreateBefore;
use Trillium\Service\Imageboard\Event\Event\ThreadCreateSuccess;
use Trillium\Service\Imageboard\Event\Event\ThreadRemove;
use Trillium\Service\Imageboard\Event\Events;
use Trillium\Service\Imageboard\PostInterface;
use Trillium\Service\Imageboard\Validator;

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
     * @var Validator
     */
    private $validator;

    /**
     * @var callable|null
     */
    private $captcha;

    /**
     * Constructor
     *
     * @param PostInterface $post      A PostInterface instance
     * @param Validator     $validator A validator instance
     * @param callable|null $captcha   A callable that takes a single argument and returns a boolean value,
     *                                 depending on whether captcha passed. If null, the check a captcha will not occur.
     *
     * @return self
     */
    public function __construct(PostInterface $post, Validator $validator, $captcha = null)
    {
        $this->post      = $post;
        $this->validator = $validator;
        $this->captcha   = $captcha;
    }

    /**
     * Checks a request data, before a thread will be created
     *
     * @param ThreadCreateBefore $event
     *
     * @return void
     */
    public function onCreateBefore(ThreadCreateBefore $event)
    {
        $request = $event->getRequest();
        $message = $request->get('message', '');
        $error   = $this->validator->post($message);
        if (is_callable($this->captcha) && !call_user_func($this->captcha, $request->get('captcha', ''))) {
            $error[] = 'Wrong captcha';
        }
        if (!empty($error)) {
            $event->setError($error);
        }
    }

    /**
     * Creates a post after thread will be created
     *
     * @param ThreadCreateSuccess $event
     *
     * @return void
     */
    public function onCreateSuccess(ThreadCreateSuccess $event)
    {
        $this->post->create($event->getBoard(), $event->getThread(), $event->getRequest()->get('message'), time());
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
            Events::THREAD_CREATE_BEFORE  => 'onCreateBefore',
            Events::THREAD_CREATE_SUCCESS => 'onCreateSuccess',
            Events::THREAD_REMOVE         => 'onRemove',
        ];
    }

}
