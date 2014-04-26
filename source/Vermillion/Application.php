<?php

/**
 * Part of the Vermillion
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Vermillion
 */

namespace Vermillion;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernel;
use Vermillion\Provider\ServiceProviderInterface;
use Vermillion\Provider\SubscriberProviderInterface;

/**
 * Application Class
 *
 * @package Vermillion
 */
class Application
{

    /**
     * Version
     */
    const VERSION = '1.0.0-dev';

    /**
     * @var Container A container instance
     */
    private $container;

    /**
     * @var array Service/Subscriber Providers
     */
    private $providers;

    /**
     * @var boolean Is services registered
     */
    private $isServicesRegistered;

    /**
     * @var boolean Is subscribers registered
     */
    private $isSubscribersRegistered;

    /**
     * Constructor
     *
     * @param Container $container A container instance
     *
     * @return self
     */
    public function __construct(Container $container = null)
    {
        $this->container               = $container ? : new Container();
        $this->isServicesRegistered    = false;
        $this->isSubscribersRegistered = false;
    }

    /**
     * Handles a request to convert it to response.
     * Sends a response.
     * Terminates a request/response cycle.
     *
     * @param Request $request
     *
     * @return void
     */
    public function run(Request $request)
    {
        $this->registerServices()->registerSubscribers();
        /** @var $kernel HttpKernel */
        $kernel   = $this->container['http_kernel'];
        $response = $kernel->handle($request);
        $response->send();
        $kernel->terminate($request, $response);
    }

    /**
     * Registers event subscribers
     *
     * @return $this
     * @throws \RuntimeException
     */
    public function registerSubscribers()
    {
        if ($this->isSubscribersRegistered) {
            return $this;
        }
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container['dispatcher'];
        foreach ($this->getProviders() as $provider) {
            if ($provider instanceof SubscriberProviderInterface) {
                foreach ($provider->getSubscribers($this->container) as $subscriber) {
                    $dispatcher->addSubscriber($subscriber);
                }
            }
        }
        $this->isSubscribersRegistered = true;

        return $this;
    }

    /**
     * Returns the service providers collection
     *
     * @return array
     * @throws \RuntimeException
     */
    private function getProviders()
    {
        if (!is_array($this->providers)) {
            $this->providers = [];
            /** @var $configuration \Vermillion\Configuration\Configuration */
            $configuration = $this->container['configuration'];
            $providers     = $configuration->load('provider')->get();
            foreach ($providers as $className) {
                if (!class_exists($className)) {
                    throw new \RuntimeException(sprintf('Provider "%s" does not exists', $className));
                }
                $this->providers[] = new $className();
            }
        }

        return $this->providers;
    }

    /**
     * Registers services
     *
     * @throws \RuntimeException
     *
     * @return $this
     */
    public function registerServices()
    {
        if ($this->isServicesRegistered) {
            return $this;
        }
        foreach ($this->getProviders() as $provider) {
            if ($provider instanceof ServiceProviderInterface) {
                $provider->registerServices($this->container);
            }
        }
        $this->isServicesRegistered = true;

        return $this;
    }

    /**
     * Returns a container instance
     *
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

}
