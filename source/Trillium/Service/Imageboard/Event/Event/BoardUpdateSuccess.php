<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Imageboard\Event\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * BoardUpdateSuccess Class
 *
 * @package Trillium\Service\Imageboard\Event\Event
 */
class BoardUpdateSuccess extends Event
{

    /**
     * @var string New name
     */
    private $newName;

    /**
     * @var string New summary
     */
    private $newSummary;

    /**
     * @var string Old name
     */
    private $oldName;

    /**
     * Construct
     *
     * @param string $newName    New name
     * @param string $newSummary New summary
     * @param string $oldName    Old name
     *
     * @return self
     */
    public function __construct($newName, $newSummary, $oldName)
    {
        $this->newName    = $newName;
        $this->newSummary = $newSummary;
        $this->oldName    = $oldName;
    }

    /**
     * Returns new name of the board
     *
     * @return string
     */
    public function getNewName()
    {
        return $this->newName;
    }

    /**
     * Returns new summary of the board
     *
     * @return string
     */
    public function getNewSummary()
    {
        return $this->newSummary;
    }

    /**
     * Returns old name of the board
     *
     * @return string
     */
    public function getOldName()
    {
        return $this->oldName;
    }

}
