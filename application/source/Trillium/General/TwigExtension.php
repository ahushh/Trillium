<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\General;

use Twig_Extension;

/**
 * TwigExtension Class
 *
 * @package Trillium\General
 */
class TwigExtension extends Twig_Extension
{

    /**
     * @var string Http host
     */
    private $httpHost;

    /**
     * Constructor
     *
     * @param string $httpHost Http host
     *
     * @return self
     */
    public function __construct($httpHost)
    {
        $this->httpHost = rtrim($httpHost, '/');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'trillium';
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [new \Twig_SimpleFunction('assets', [$this, 'assets'])];
    }

    /**
     * {@inheritdoc}
     */
    public function getGlobals()
    {
        return ['homepage_url' => $this->httpHost];
    }

    /**
     * Assets function
     *
     * Returns an absolute url for a resource
     *
     * @param string $url Path to the resource
     *
     * @return string
     */
    public function assets($url = '')
    {
        return $this->httpHost . '/assets/' . ltrim($url, '/');
    }

}
