<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Twig;

use Symfony\Bridge\Twig\TwigEngine as SymfonyTwigEngine;

/**
 * TwigEngine Class
 *
 * @package Trillium\Service\Twig
 */
class TwigEngine extends SymfonyTwigEngine
{

    /**
     * Returns a twig environment instance
     *
     * @return \Twig_Environment
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

}
