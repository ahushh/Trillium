<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Provider;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Http\Firewall;
use Symfony\Component\Security\Http\RememberMe\ResponseListener;
use Trillium\Service\Security\Container;

/**
 * SecurityProvider Class
 *
 * @package Trillium\Provider
 */
class SecurityProvider
{

    /**
     * @var SecurityContext Security context
     */
    private $securityContext;

    /**
     * @var Firewall Firewall
     */
    private $firewall;

    /**
     * @var ResponseListener RememberMe response listener
     */
    private $rememberMeListener;

    /**
     * Constructor
     *
     * @param array                    $config      Configuration
     * @param int                      $httpPort    HTTP port
     * @param int                      $httpsPort   HTTPS port
     * @param HttpKernelInterface      $kernel      Http kernel interface
     * @param EventDispatcherInterface $dispatcher  Event dispatcher interface
     * @param UrlGeneratorInterface    $generator   Url generator interface
     * @param UrlMatcherInterface      $matcher     Url matcher interface
     * @param LoggerInterface|null     $logger      Logger interface
     *
     * @return self
     */
    public function __construct(
        array                    $config,
        $httpPort,
        $httpsPort,
        HttpKernelInterface      $kernel,
        EventDispatcherInterface $dispatcher,
        UrlGeneratorInterface    $generator,
        UrlMatcherInterface      $matcher,
        LoggerInterface          $logger = null
    )
    {
        $values = array_merge(
            [
                'http_port'     => $httpPort,
                'https_port'    => $httpsPort,
                'http_kernel'   => $kernel,
                'logger'        => $logger,
                'dispatcher'    => $dispatcher,
                'url_generator' => $generator,
                'url_matcher'   => $matcher
            ],
            $config
        );
        $container                = new Container($values);
        $this->firewall           = $container->getFirewall();
        $this->rememberMeListener = $container->getRememberMeResponseListener();
        $this->securityContext    = $container->getSecurityContext();
    }

    /**
     * Returns the security context instance
     *
     * @return SecurityContext
     */
    public function securityContext()
    {
        return $this->securityContext;
    }

    /**
     * Returns the firewall instance
     *
     * @return Firewall
     */
    public function firewall()
    {
        return $this->firewall;
    }

    /**
     * Returns the RememberMe response listener instance
     *
     * @return ResponseListener
     */
    public function rememberMeListener()
    {
        return $this->rememberMeListener;
    }

}
