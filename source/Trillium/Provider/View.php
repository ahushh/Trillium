<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Provider;

use Kilte\View\Environment;
use Kilte\View\Loader;
use Kilte\View\Macros;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Router;
use Vermillion\Container;
use Vermillion\Provider\ServiceProviderInterface;

/**
 * View Class
 *
 * @package Trillium\Provider
 */
class View implements ServiceProviderInterface
{

    /**
     * {@inheritdoc}
     */
    public function registerServices(Container $container)
    {
        $container['view.macros'] = function ($container) {
            return (new Macros())->register(
                [
                    'url'       => function ($name, $params = []) use ($container) {
                            static $generator = null;
                            if ($generator === null) {
                                /** @var $router Router */
                                $router    = $container['router'];
                                $generator = $router->getGenerator();
                            }

                            return $generator->generate($name, $params, UrlGeneratorInterface::ABSOLUTE_URL);
                        },
                    'static'    => function ($path) use ($container) {
                            static $baseUrl = null;
                            if ($baseUrl === null) {
                                /** @var $router Router */
                                $router  = $container['router'];
                                $context = $router->getContext();
                                $baseUrl = $context->getScheme() . '://'
                                    . $context->getHost() . '/'
                                    . $context->getBaseUrl();
                            }

                            return $baseUrl . 'static/' . ltrim($path, '/');
                        },
                    '_'         => function ($id, array $params = [], $domain = null, $locale = null) use ($container) {
                            static $translator = null;
                            if ($translator === null) {
                                /** @var $translator \Symfony\Component\Translation\TranslatorInterface */
                                $translator = $container['translator'];
                            }

                            return $translator->trans($id, $params, $domain, $locale);
                        },
                    'isGranted' => function ($attributes, $object = null) use ($container) {
                            static $security = null;
                            if ($security === null) {
                                /** @var $security \Symfony\Component\Security\Core\SecurityContextInterface */
                                $security = $container['security'];
                            }

                            return $security->isGranted($attributes, $object);
                        },
                ]
            );
        };
        $container['view']        = function ($container) {
            /** @var $env \Vermillion\Environment */
            $env    = $container['environment'];
            $loader = new Loader();
            $loader->pushDir($env->getDirectory('views'));

            return new Environment($loader, $container['view.macros'], $env->isDebug());
        };
    }

}
