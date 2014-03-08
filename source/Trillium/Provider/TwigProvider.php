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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Templating\TemplateNameParser;
use Symfony\Component\Translation\TranslatorInterface;
use Trillium\Service\Twig\RequestListener;
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
     * @var TwigEngine
     */
    private $twig;

    /**
     * @var RequestListener
     */
    private $requestListener;

    /**
     * Constructor
     *
     * @param string                $directory  Path to the views directory
     * @param boolean               $debug      Is debug
     * @param string                $charset    Character set
     * @param string                $cache      Path to the cache directory
     * @param TranslatorInterface   $translator Translator interface
     * @param UrlGeneratorInterface $generator  Url generator interface
     *
     * @return self
     */
    public function __construct(
        $directory,
        $debug,
        $charset,
        $cache,
        TranslatorInterface $translator,
        UrlGeneratorInterface $generator
    )
    {
        $twig = new Twig_Environment(
            new Twig_Loader_Filesystem($directory),
            [
                'debug'            => $debug,
                'charset'          => $charset,
                'cache'            => $cache,
                'strict_variables' => true,
            ]
        );
        $twig->setExtensions(
            [
                new Twig_Extension_Core(),
                new TranslationExtension($translator),
                new RoutingExtension($generator)
            ]
        );
        $this->twig = new TwigEngine($twig, new TemplateNameParser());
        $this->requestListener = new RequestListener($twig);
    }

    /**
     * Returns the twig-bridge instance
     *
     * @return TwigEngine
     */
    public function twig()
    {
        return $this->twig;
    }

    /**
     * Returns the RequestListener instance
     *
     * @return RequestListener
     */
    public function requestListener()
    {
        return $this->requestListener;
    }

}
