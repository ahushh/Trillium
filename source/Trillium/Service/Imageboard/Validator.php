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
     * Validates thread title
     *
     * @param string $title Title
     *
     * @return array
     */
    public function thread($title)
    {
        $error    = [];
        $titleLen = strlen($title);
        if ($titleLen < 2 || $titleLen > 30) {
            $error[] = sprintf('Title must be between %d and %d characters', 2, 30);
        }

        return $error;
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
        $error      = [];
        $messageLen = mb_strlen($message);
        if ($messageLen < 2 || $messageLen > 10000) {
            $error[] = sprintf('Message must be between %d and %d characters', 2, 10000);
        }

        return $error;
    }

}
