<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Imageboard;

/**
 * Validator Class
 *
 * @package Trillium\Service\Imageboard
 */
class Validator
{

    /**
     * Validates board data
     *
     * @param string $name    Name
     * @param string $summary Summary
     *
     * @return array
     */
    public function board($name, $summary)
    {
        $error = [];
        if (empty($name)) {
            $error[] = 'Name is required';
        } elseif (preg_match('~[^a-z0-9]~', $name)) {
            $error[] = 'Name must contain only letters a-z and/or numbers 0-9';
        } elseif (strlen($name) > 10) {
            $error[] = sprintf('Name must contain less than %d characters');
        }
        $summaryLen = strlen($summary);
        if ($summaryLen < 2 || $summary > 100) {
            $error[] = sprintf('Summary must be between %d and %d characters', 2, 100);
        }

        return $error;
    }

    /**
     * Validates thread data
     *
     * @param string $title   Thread title
     * @param string $message Message
     *
     * @return array
     */
    public function thread($title, $message)
    {
        $error = [$this->post($message), $this->threadTitle($title)];
        foreach ($error as $k => $v) {
            if (empty($v)) {
                unset($error[$k]);
            }
        }

        return $error;
    }

    /**
     * Validates thread title
     *
     * @param string $title Title
     *
     * @return string
     */
    public function threadTitle($title)
    {
        $titleLen = strlen($title);

        return $titleLen < 2 || $titleLen > 30 ? sprintf('Title must be between %d and %d characters', 2, 30) : '';
    }

    /**
     * Validates post data
     *
     * @param string $message A message
     *
     * @return string
     */
    public function post($message)
    {
        $messageLen = mb_strlen($message);

        return $messageLen < 2 || $messageLen > 10000
            ? sprintf('Message must be between %d and %d characters', 2, 10000)
            : '';
    }

}
