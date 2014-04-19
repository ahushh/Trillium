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
use Trillium\Service\Imageboard\Event\Event\PostCreateBefore;
use Trillium\Service\Imageboard\Event\Event\PostCreateSuccess;
use Trillium\Service\Imageboard\Event\Event\PostRemove;
use Trillium\Service\Imageboard\Event\Events;
use Trillium\Service\Imageboard\ImageInterface;

/**
 * Post Class
 *
 * @package Trillium\Service\Imageboard\Event\Listener
 */
class Post implements EventSubscriberInterface
{

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
     * @param ImageService   $imageService ImageService instance
     * @param ImageInterface $image        ImageInterface instance
     * @param callable|null  $captcha      A callable that takes a single argument and returns a boolean value,
     *                                     depending on whether captcha passed.
     *                                     If null, the check a captcha will not occur.
     *
     * @return self
     */
    public function __construct(ImageService $imageService, ImageInterface $image, $captcha = null)
    {
        $this->imageService = $imageService;
        $this->image        = $image;
        $this->captcha      = $captcha;
    }

    /**
     * @param PostCreateBefore $event
     *
     * @return void
     */
    public function onCreateBefore(PostCreateBefore $event)
    {
        $request = $event->getRequest();
        $error   = [];
        if (is_callable($this->captcha) && !call_user_func($this->captcha, $request->get('captcha', ''))) {
            $error[] = 'Wrong captcha';
        }
        $file = $request->files->get('file');
        if ($file instanceof UploadedFile) {
            $this->imageService->setFile($file)->validate();
            $error = array_merge($error, $this->imageService->getError());
        }
        $event->setError($error);
    }

    /**
     * @param PostCreateSuccess $event
     *
     * @return void
     */
    public function onCreateSuccess(PostCreateSuccess $event)
    {
        $request = $event->getRequest();
        $post    = $event->getPost();
        $file    = $request->files->get('file');
        if ($file instanceof UploadedFile) {
            $this->imageService->upload($event->getThread(), $post, $post . '_preview');
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
     * @param PostRemove $event
     *
     * @return void
     */
    public function onRemove(PostRemove $event)
    {
        $post = $event->getPost();
        $image = $this->image->get($post);
        if (is_array($image)) {
            $this->imageService->remove($image['thread'] . '/' . $post, $image['ext'], $image['thread'] . '/' . $post . '_preview');
            $this->image->remove($post);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::POST_CREATE_BEFORE  => 'onCreateBefore',
            Events::POST_CREATE_SUCCESS => 'onCreateSuccess',
            Events::POST_REMOVE         => 'onRemove',
        ];
    }

}
