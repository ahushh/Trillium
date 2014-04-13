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
 * @property-read \Ciconia\Ciconia                                            $markdown
 * @property-read \Gregwar\Captcha\CaptchaBuilder                             $captcha
 * @property-read \Kilte\AccountManager\Controller\ControllerInterface        $userController
 * @property-read \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
 * @property-read \Symfony\Component\HttpFoundation\Session\SessionInterface  $session
 * @property-read \Symfony\Component\Security\Core\SecurityContextInterface   $security
 * @property-read \Trillium\Service\Date\Date                                 $date
 * @property-read \Trillium\Service\Image\Image                               $imageService
 * @property-read \Trillium\Service\Imageboard\BoardInterface                 $board
 * @property-read \Trillium\Service\Imageboard\ImageInterface                 $image
 * @property-read \Trillium\Service\Imageboard\PostInterface                  $post
 * @property-read \Trillium\Service\Imageboard\ThreadInterface                $thread
 * @property-read \Trillium\Service\Imageboard\Validator                      $validator
 * @property-read \Trillium\Service\Settings\Settings                         $settings
 * @property-read \Vermillion\Configuration\Configuration                     $configuration
 * @property-read \Vermillion\Environment                                     $environment
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
