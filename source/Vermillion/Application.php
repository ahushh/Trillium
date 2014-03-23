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
     * @var Container A container instance
     */
    private $container;

    /**
     * Constructor
     *
     * @param Container $container A container instance
     *
     * @return self
     */
    public function __construct(Container $container = null)
    {
        $this->container = $container ? : new Container();
    }

    /**
     * Registers services and event listeners
     *
     * @throws \RuntimeException
     *
     * @return $this
     */
    public function register()
    {
        /**
         * @var $configuration \Vermillion\Configuration\Configuration
         * @var $dispatcher    \Symfony\Component\EventDispatcher\EventDispatcherInterface
         */
        $configuration = $this->container['configuration'];
        $dispatcher    = $this->container['dispatcher'];
        $providers     = $configuration->load('provider')->get();
        foreach ($providers as $className) {
            if (!class_exists($className)) {
                throw new \RuntimeException(sprintf('Provider "%s" does not exists', $className));
            }
            $provider = new $className();
            if ($provider instanceof ServiceProviderInterface) {
                $provider->registerServices($this->container);
            }
            if ($provider instanceof SubscriberProviderInterface) {
                foreach ($provider->getSubscribers($this->container) as $subscriber) {
                    $dispatcher->addSubscriber($subscriber);
                }
            }
        }

        return $this;
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
        $this->register();
        /** @var $kernel HttpKernel */
        $kernel   = $this->container['http_kernel'];
        $response = $kernel->handle($request);
        $response->send();
        $kernel->terminate($request, $response);
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
