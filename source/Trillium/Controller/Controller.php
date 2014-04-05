<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Controller;

/**
 * Controller Class
 *
 * @property-read \Symfony\Component\HttpFoundation\Session\SessionInterface $session
 * @property-read \Kilte\AccountManager\Controller\ControllerInterface       $userController
 * @property-read \Symfony\Component\Security\Core\SecurityContextInterface  $security
 * @property-read \Vermillion\Configuration\Configuration                    $configuration
 * @property-read \Trillium\Service\Settings\Settings                        $settings
 * @property-read \Trillium\Service\Date\Date                                $date
 *
 * @package Trillium\Controller
 */
class Controller extends \Vermillion\Controller\Controller
{

    /**
     * Returns an item from the container by key
     *
     * @param string $key Key
     *
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function __get($key)
    {
        if (!is_string($key)) {
            throw new \InvalidArgumentException(sprintf('Expects string, %s given', $key));
        }

        return $this->container[$key];
    }

}
