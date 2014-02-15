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
     * @var string Path to the views directory
     */
    private $directory;
    /**
     * @var boolean Is debug
     */
    private $debug;
    /**
     * @var string Character set
     */
    private $charset;
    /**
     * @var string Path to the cache directory
     */
    private $cache;
    /**
     * @var TranslatorInterface Translator interface
     */
    private $translator;
    /**
     * @var UrlGeneratorInterface Url generator interface
     */
    private $generator;

    /**
     * @var TwigEngine
     */
    private $twig;

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
        $this->directory  = $directory;
        $this->debug      = $debug;
        $this->charset    = $charset;
        $this->cache      = $cache;
        $this->translator = $translator;
        $this->generator  = $generator;
    }

    /**
     * Returns the twig-bridge instance
     *
     * @return TwigEngine
     */
    public function twig()
    {
        if ($this->twig === null) {
            $loader = new Twig_Loader_Filesystem($this->directory);
            $options = [
                'debug'            => $this->debug,
                'charset'          => $this->charset,
                'cache'            => $this->cache,
                'strict_variables' => true,
            ];
            $extensions = [
                new Twig_Extension_Core(),
                new TranslationExtension($this->translator),
                new RoutingExtension($this->generator)
            ];
            $twig = new Twig_Environment($loader, $options);
            $twig->setExtensions($extensions);

            $this->twig = new TwigEngine($twig, new TemplateNameParser());
        }

        return $this->twig;
    }

}
