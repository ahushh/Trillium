<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Provider;

use Symfony\Bridge\Twig\Extension\RoutingExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Component\Templating\TemplateNameParser;
use Trillium\General\Application;
use Trillium\Service\Twig\TwigEngine;
use Twig_Environment;
use Twig_Extension_Core;
use Twig_Loader_Filesystem;

/**
 * TwigProvider Class
 *
 * @package Trillium\Provider
 */
class TwigProvider
{

    /**
     * Creates the twig-bridge instance
     *
     * @param Application $app An application instance
     *
     * @return TwigEngine
     */
    public function register(Application $app)
    {
        $loader = new Twig_Loader_Filesystem($app->getViewsDir());
        $options = [
            'debug'            => $app->isDebug(),
            'charset'          => $app->configuration->get('charset', 'UTF-8'),
            'cache'            => $app->getCacheDir() . 'twig',
            'strict_variables' => true,
        ];
        $extensions = [
            new Twig_Extension_Core(),
            new TranslationExtension($app->translator),
            new RoutingExtension($app->router->getGenerator())
        ];
        $twig = new Twig_Environment($loader, $options);
        $twig->setExtensions($extensions);

        return new TwigEngine($twig, new TemplateNameParser());
    }

}
