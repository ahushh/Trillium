<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\General;

use Symfony\Component\Debug\ExceptionHandler;

/**
 * DebugExceptionHandler Class
 *
 * Overrides the handle() method
 * Clear output buffer before handle exception
 *
 * @package Trillium\General
 */
class DebugExceptionHandler extends ExceptionHandler
{

    /**
     * {@inheritdoc}
     */
    public function handle(\Exception $exception)
    {
        while (ob_get_level()) {
            ob_end_clean();
        }
        parent::handle($exception);
    }

}
