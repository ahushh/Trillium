<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Date;

/**
 * Date Class
 *
 * @package Trillium\Service\Date
 */
class Date
{

    /**
     * @var int Timeshift
     */
    private $timeshift;

    /**
     * Constructor
     *
     * @param int $timeshift Timeshift (+-12)
     *
     * @return self
     */
    public function __construct($timeshift)
    {
        $this->timeshift = $timeshift;
    }

    /**
     * Format date with timeshift
     *
     * @param int $timestamp Timestamp
     *
     * @see date
     * @return boolean|string
     */
    public function format($timestamp)
    {
        return date('d.m.Y / H:i:s', $timestamp + $this->timeshift);
    }

}
