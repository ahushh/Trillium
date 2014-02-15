<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Provider;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;
use Trillium\Service\Configuration\Configuration;
use Trillium\Service\Configuration\PhpFileLoader;
use Trillium\Service\Configuration\YamlFileLoader;

/**
 * ConfigurationProvider Class
 *
 * @package Trillium\Provider
 */
class ConfigurationProvider
{

    /**
     * @var string Environment
     */
    private $environment;

    /**
     * @var Configuration Configuration service
     */
    private $configuration;

    /**
     * Constructor
     *
     * @param string $environment Environment
     *
     * @return self
     */
    public function __construct($environment)
    {
        $this->environment   = $environment;
        $this->configuration = null;
    }

    /**
     * Returns configuration service
     *
     * @return Configuration
     */
    public function configuration()
    {
        if ($this->configuration === null) {
            $this->configuration = new Configuration($this->environment, new LoaderResolver());
            $configResolver      = $this->configuration->getResolver();
            $configFileLocator = new FileLocator($this->configuration->getPaths());
            $configResolver->addLoader(new PhpFileLoader($configFileLocator));
            $configResolver->addLoader(new YamlFileLoader($configFileLocator));
            $this->configuration->setDefault('application', 'yml');
        }

        return $this->configuration;
    }

}
