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
use Trillium\Service\Image\Validator;
use Trillium\Service\Imageboard\Event\Event\PostCreateBefore;
use Trillium\Service\Imageboard\Event\Event\PostCreateSuccess;
use Trillium\Service\Imageboard\Event\Event\PostRemove;
use Trillium\Service\Imageboard\Event\Events;
use Trillium\Service\Imageboard\Exception\ImageNotFoundException;
use Trillium\Service\Imageboard\ImageInterface;
use Trillium\Service\Imageboard\Traits\FileUpload;

/**
 * Post Class
 *
 * @package Trillium\Service\Imageboard\Event\Listener
 */
class Post implements EventSubscriberInterface
{

    use FileUpload;

    /**
     * A callable that takes a single argument and returns a boolean value,
     * depending on whether captcha passed.
     * If null, the check a captcha will not occur.
     *
     * @var callable|null
     */
    private $captcha;

    /**
     * @var ImageInterface ImageInterface instance
     */
    private $image;

    /**
     * @var Validator Images validator
     */
    private $imageValidator;

    /**
     * @var Manager Manager instance
     */
    private $manager;

    /**
     * Constructor
     *
     * @param Validator      $validator Images validator
     * @param ImageInterface $image     ImageInterface instance
     * @param Manager        $manager   Manager instance
     * @param callable|null  $captcha   A callable that takes a single argument and returns a boolean value,
     *                                  depending on whether captcha passed.
     *                                  If null, the check a captcha will not occur.
     *
     * @return self
     */
    public function __construct(
        Validator $validator,
        ImageInterface $image,
        Manager $manager,
        $captcha = null
    ) {
        $this->image          = $image;
        $this->captcha        = $captcha;
        $this->imageValidator = $validator;
        $this->manager        = $manager;
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

    /**
     * Performs before post will be created
     *
     * @param PostCreateBefore $event An event
     *
     * @return void
     */
    public function onCreateBefore(PostCreateBefore $event)
    {
        $request = $event->getRequest();
        $error   = $this->validateFile($request);
        if (is_callable($this->captcha) && !call_user_func($this->captcha, $request->get('captcha', ''))) {
            $error[] = 'Wrong captcha';
        }
        $event->setError($error);
    }

    /**
     * Performs after a post was created
     *
     * @param PostCreateSuccess $event An event
     *
     * @return void
     */
    public function onCreateSuccess(PostCreateSuccess $event)
    {
        $this->uploadFile($event->getRequest(), $event->getBoard(), $event->getThread(), $event->getPost());
    }

    /**
     * Performs after post was removed
     *
     * @param PostRemove $event An event
     *
     * @return void
     */
    public function onRemove(PostRemove $event)
    {
        $post = $event->getPost();
        try {
            $image = $this->image->get($post);
            $this->manager->remove(
                [
                    $image['thread'] . '/' . $post . '.' . $image['ext'],
                    $image['thread'] . '/' . $post . Manager::THUMBNAIL_POSTFIX
                ]
            );
            $this->image->remove($post);
        } catch (ImageNotFoundException $e) {
        }
    }

}
