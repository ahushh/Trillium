<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Imageboard\Event\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Trillium\Service\Image\Image as ImageService;
use Trillium\Service\Imageboard\Event\Event\ThreadCreateBefore;
use Trillium\Service\Imageboard\Event\Event\ThreadCreateSuccess;
use Trillium\Service\Imageboard\Event\Event\ThreadRemove;
use Trillium\Service\Imageboard\Event\Events;
use Trillium\Service\Imageboard\ImageInterface;
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
     * @var ImageService
     */
    private $imageService;

    /**
     * @var ImageInterface
     */
    private $image;

    /**
     * Constructor
     *
     * @param PostInterface  $post         A PostInterface instance
     * @param Validator      $validator    A validator instance
     * @param ImageService   $imageService An ImageService instance
     * @param ImageInterface $image        An ImageInterface instance
     * @param callable|null  $captcha      A callable that takes a single argument and returns a boolean value,
     *                                     depending on whether captcha passed. If null, the check a captcha will not occur.
     *
     * @return self
     */
    public function __construct(
        PostInterface $post,
        Validator $validator,
        ImageService $imageService,
        ImageInterface $image,
        $captcha = null
    ) {
        $this->post         = $post;
        $this->validator    = $validator;
        $this->captcha      = $captcha;
        $this->imageService = $imageService;
        $this->image        = $image;
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
        if (is_callable($this->captcha) && !call_user_func($this->captcha, $request->get('captcha', ''))) {
            $error[] = 'Wrong captcha';
        }
        $file = $request->files->get('file');
        if ($file instanceof UploadedFile) {
            $this->imageService->setFile($file)->validate();
            $error = array_merge($error, $this->imageService->getError());
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
        $request = $event->getRequest();
        $post    = $this->post->create($event->getBoard(), $event->getThread(), $request->get('message'), time());
        $file    = $request->files->get('file');
        if ($file instanceof UploadedFile) {
            $this->imageService->upload($post, $post . '_preview');
            $this->image->create(
                $event->getBoard(),
                $event->getThread(),
                $post,
                $file->getClientOriginalExtension(),
                $this->imageService->getImageWidth(),
                $this->imageService->getImageHeight(),
                (int) round($file->getClientSize() / 1024, 0)
            );
        }
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
        $this->post->removeThread($thread);
        $images = $this->image->getThread($thread);
        if (!empty($images)) {
            foreach ($images as $image) {
                $this->imageService->remove($image['post'], $image['ext'], $image['post'] . '_preview');
            }
            $this->image->removeThread($thread);
        }
    }

}
