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
use Trillium\Service\Image\Validator as ImageValidator;
use Trillium\Service\Imageboard\Event\Event\ThreadCreateBefore;
use Trillium\Service\Imageboard\Event\Event\ThreadCreateSuccess;
use Trillium\Service\Imageboard\Event\Event\ThreadRemove;
use Trillium\Service\Imageboard\Event\Events;
use Trillium\Service\Imageboard\ImageInterface;
use Trillium\Service\Imageboard\PostInterface;
use Trillium\Service\Imageboard\Traits\FileUpload;
use Trillium\Service\Imageboard\Validator;

/**
 * Thread Class
 *
 * @package Trillium\Service\Imageboard\Event\Listener
 */
class Thread implements EventSubscriberInterface
{

    use FileUpload;

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
     * @var ImageInterface
     */
    private $image;

    /**
     * @var ImageValidator
     */
    private $imageValidator;

    /**
     * @var Manager
     */
    private $manager;

    /**
     * Constructor
     *
     * @param PostInterface  $post           A PostInterface instance
     * @param Validator      $validator      A validator instance
     * @param ImageValidator $imageValidator An ImageValidator instance
     * @param Manager        $manager        Manager instance
     * @param ImageInterface $image          An ImageInterface instance
     * @param callable|null  $captcha        A callable that takes a single argument and returns a boolean value,
     *                                       depending on whether captcha passed.
     *                                       If null, the check a captcha will not occur.
     *
     * @return self
     */
    public function __construct(
        PostInterface $post,
        Validator $validator,
        ImageValidator $imageValidator,
        Manager $manager,
        ImageInterface $image,
        $captcha = null
    ) {
        $this->post           = $post;
        $this->validator      = $validator;
        $this->captcha        = $captcha;
        $this->image          = $image;
        $this->imageValidator = $imageValidator;
        $this->manager        = $manager;
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
        $error   = array_merge($error, $this->validateFile($request));
        if (is_callable($this->captcha) && !call_user_func($this->captcha, $request->get('captcha', ''))) {
            $error[] = 'Wrong captcha';
        }
        $event->setError($error);
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
        $thread  = $event->getThread();
        $request = $event->getRequest();
        $board   = $event->getBoard();
        $post    = $this->post->create($board, $thread, $request->get('message'), time());
        $this->manager->makeDirectory($thread);
        $this->uploadFile($request, $board, $thread, $post);
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
        $thread = $event->getThread();
        $this->manager->remove(new \FilesystemIterator($this->manager->getDirectory($thread)));
        $this->post->removeThread($thread);
        $this->image->removeThread($thread);
    }

}
