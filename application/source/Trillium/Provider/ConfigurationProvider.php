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
     * @var Configuration Configuration service
     */
    private $configuration;

    /**
     * Constructor
     *
     * @param array       $paths           List of directories
     * @param string|null $defaultResource Default resource
     * @param string|null $defaultType     Type of the default resource
     *
     * @return self
     */
    public function __construct(array $paths, $defaultResource = null, $defaultType = null)
    {
        $fileLocator         = new FileLocator($paths);
        $resolver            = new LoaderResolver();
        $resolver->addLoader(new PhpFileLoader($fileLocator));
        $resolver->addLoader(new YamlFileLoader($fileLocator));
        $this->configuration = new Configuration($resolver);
        if ($defaultResource !== null && $defaultType !== null) {
            $this->configuration->setDefault($defaultResource, $defaultType);
        }
    }

    /**
     * Returns configuration service
     *
     * @return Configuration
     */
    public function configuration()
    {
        return $this->configuration;
    }

}
