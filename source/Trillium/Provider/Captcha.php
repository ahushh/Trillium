<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Provider;

use Gregwar\Captcha\CaptchaBuilder;
use Vermillion\Container;
use Vermillion\Provider\ServiceProviderInterface;

/**
 * Captcha Class
 *
 * @package Trillium\Provider
 */
class Captcha implements ServiceProviderInterface
{

    /**
     * Session key
     */
    const SESSION_KEY = 'gregwar_captcha';

    /**
     * {@inheritdoc}
     */
    public function registerServices(Container $container)
    {
        $container['captcha']      = function () {
            return new CaptchaBuilder();
        };
        $container['captcha.test'] = $container->protect(
            function ($phrase) use ($container) {
                /** @var $captcha CaptchaBuilder */
                $captcha = $container['captcha'];
                /** @var $session \Symfony\Component\HttpFoundation\Session\SessionInterface */
                $session = $container['session'];
                $captcha->setPhrase($session->get(self::SESSION_KEY));

                return $captcha->testPhrase($phrase);
            }
        );
    }

}
