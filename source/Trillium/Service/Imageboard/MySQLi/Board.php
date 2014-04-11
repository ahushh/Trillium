<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Imageboard\MySQLi;

use Trillium\Service\Imageboard\BoardInterface;
use Trillium\Service\Imageboard\Exception\BoardNotFoundException;

/**
 * Board Class
 *
 * @package Trillium\Service\Imageboard\MySQLi
 */
class Board extends MySQLi implements BoardInterface
{

    /**
     * {@inheritdoc}
     */
    public function create($name, $summary)
    {
        $this->mysqli->query(
            sprintf(
                "INSERT INTO `%s` SET `name` = '%s', `summary` = '%s'",
                $this->tableName,
                $this->mysqli->real_escape_string($name),
                $this->mysqli->real_escape_string($summary)
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function update($newName, $summary, $name)
    {
        $this->mysqli->query(
            sprintf(
                "UPDATE `%s` SET `name` = '%s', `summary` = '%s' WHERE `name` = '%s'",
                $this->tableName,
                $this->mysqli->real_escape_string($newName),
                $this->mysqli->real_escape_string($summary),
                $this->mysqli->real_escape_string($name)
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function delete($name)
    {
        return parent::removeItem('name', $name);
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        $board = parent::getItem('name', $name);
        if ($board === null) {
            throw new BoardNotFoundException($name);
        }

        return $board;
    }

    /**
     * {@inheritdoc}
     */
    public function listing()
    {
        return parent::listingItems(sprintf("SELECT * FROM `%s` ORDER BY `name` ASC", $this->tableName));
    }

    /**
     * {@inheritdoc}
     */
    public function isExists($name)
    {
        return parent::isItemExists('name', $name);
    }

}
