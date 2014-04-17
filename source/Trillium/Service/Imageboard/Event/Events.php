<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Imageboard\Event;

/**
 * Events Class
 *
 * @package Trillium\Service\Imageboard\Event
 */
final class Events
{

    /**
     * This event occurs when a board was removed
     *
     * The event listener method
     * receives a Trillium\Service\Imageboard\Event\Event\BoardRemove
     * instance.
     *
     * @see \Trillium\Service\Imageboard\Event\Event\BoardRemove
     */
    const BOARD_REMOVE = 'trillium.board.remove';

    /**
     * This event occurs when data of a board is updated
     *
     * The event listener method
     * receives a Trillium\Service\Imageboard\Event\Event\BoardUpdateSuccess
     * instance.
     *
     * @see \Trillium\Service\Imageboard\Event\Event\BoardUpdateSuccess
     */
    const BOARD_UPDATE_SUCCESS = 'trillium.board.update_success';

    /**
     * This event occurs when a thread was removed
     *
     * The event listener method
     * receives a Trillium\Service\Imageboard\Event\Event\ThreadRemove
     * instance.
     *
     * @see \Trillium\Service\Imageboard\Event\Event\ThreadRemove
     */
    const THREAD_REMOVE = 'trillium.thread.remove';

    /**
     * This event occurs before a thread will be created
     *
     * You can use it to check a request data and set errors.
     * The event listener method
     * receives a Trillium\Service\Imageboard\Event\Event\ThreadCreateBefore
     * instance.
     *
     * @see \Trillium\Service\Imageboard\Event\Event\ThreadCreateBefore
     */
    const THREAD_CREATE_BEFORE = 'trillium.thread.create.before';

    /**
     * This event occurs after a thread will be created
     *
     * The event listener method
     * receives a Trillium\Service\Imageboard\Event\Event\ThreadCreateSuccess
     * instance.
     *
     * @see \Trillium\Service\Imageboard\Event\Event\ThreadCreateSuccess
     */
    const THREAD_CREATE_SUCCESS = 'trillium.thread.create.success';

    /**
     * This event occurs before a post will be created
     *
     * You can use it to check a request data and set errors.
     * The event listener method
     * receives a Trillium\Service\Imageboard\Event\Event\PostCreateBefore
     * instance.
     *
     * @see Trillium\Service\Imageboard\Event\Event\PostCreateBefore
     */
    const POST_CREATE_BEFORE = 'trillium.post.create.before';

    /**
     * This event occurs after a post will be created
     *
     * The event listener method
     * receives a Trillium\Service\Imageboard\Event\Event\PostCreateSuccess
     * instance.
     *
     * @see \Trillium\Service\Imageboard\Event\Event\PostCreateSuccess
     */
    const POST_CREATE_SUCCESS = 'trillium.post.create.success';

    /**
     * This event occurs when a post was removed
     *
     * The event listener method
     * receives a Trillium\Service\Imageboard\Event\Event\PostRemove
     * instance.
     *
     * @see \Trillium\Service\Imageboard\Event\Event\PostRemove
     */
    const POST_REMOVE = 'trillium.post.remove';

}
