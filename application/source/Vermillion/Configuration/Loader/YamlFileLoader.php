<?php

/**
 * Part of the Vermillion
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Vermillion
 */

namespace Vermillion\Configuration\Loader;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Yaml\Parser;

/**
 * YamlFileLoader Class
 *
 * @package Vermillion\Configuration\Loader
 */
class YamlFileLoader extends Loader
{

    /**
     * @var Parser
     */
    private $parser;

    /**
     * Constructor
     *
     * @param FileLocatorInterface $locator
     * @param Parser               $parser
     *
     * @return self
     */
    public function __construct(FileLocatorInterface $locator, Parser $parser)
    {
        $this->parser = $parser;
        parent::__construct($locator);
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        return $this->parser->parse(file_get_contents($this->locator->locate($resource)));
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return pathinfo($this->locator->locate($resource), PATHINFO_EXTENSION) === 'yml';
    }

}
