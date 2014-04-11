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

}
