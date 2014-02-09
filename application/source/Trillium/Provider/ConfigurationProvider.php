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
use Trillium\General\Application;
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
     * Creates the Configuration instance
     *
     * @param Application $app An application instance
     *
     * @return Configuration
     */
    public function register(Application $app)
    {
        $configuration     = new Configuration($app->getEnvironment(), new LoaderResolver());
        $configResolver    = $configuration->getResolver();
        $configFileLocator = new FileLocator($configuration->getPaths());
        $configResolver->addLoader(new PhpFileLoader($configFileLocator));
        $configResolver->addLoader(new YamlFileLoader($configFileLocator));
        $configuration->setDefault('application', 'yml');
        $app->setLocale($configuration->get('locale', $app->getLocale()));

        return $configuration;
    }

}
