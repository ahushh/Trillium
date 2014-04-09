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
    public function get($name)
    {
        $result = $this->mysqli->query(
            sprintf(
                "SELECT * FROM `%s` WHERE `name` = '%s'",
                $this->tableName,
                $this->mysqli->real_escape_string($name)
            )
        );
        $board  = $result->fetch_assoc();
        $result->free();
        if (!is_array($board)) {
            throw new BoardNotFoundException($name);
        }

        return $board;
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
        $this->mysqli->query(
            sprintf(
                "DELETE FROM `%s` WHERE `name` = '%s'",
                $this->tableName,
                $this->mysqli->real_escape_string($name)
            )
        );

        return $this->mysqli->affected_rows;
    }

    /**
     * {@inheritdoc}
     */
    public function listing()
    {
        $list   = [];
        $result = $this->mysqli->query(sprintf("SELECT * FROM `%s` ORDER BY `name` ASC", $this->tableName));
        while (($board = $result->fetch_assoc())) {
            $list[] = $board;
        }
        $result->free();

        return $list;
    }

    /**
     * {@inheritdoc}
     */
    public function isExists($name)
    {
        $result = $this->mysqli->query(
            sprintf(
                "SELECT COUNT(*) FROM `%s` WHERE `name` = '%s'",
                $this->tableName,
                $this->mysqli->real_escape_string($name)
            )
        );
        $total  = (int) $result->fetch_row()[0];
        $result->free();

        return $total > 0;
    }

}
