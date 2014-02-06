<?php

/**
 * Part of the Trillium
 *
 * @author Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\General;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimple;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Debug\Debug;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\EventListener\ResponseListener;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Router;
use Symfony\Component\Translation\Loader\JsonFileLoader;
use Symfony\Component\Translation\Translator;
use Trillium\General\Configuration\Configuration;
use Trillium\General\EventListener\ExceptionListener;
use Trillium\General\EventListener\LocaleListener;

/**
 * Application Class
 *
 * @property-read EventDispatcher    $dispatcher
 * @property-read Logger             $logger
 * @property-read ControllerResolver $resolver
 * @property-read RequestStack       $requestStack
 * @property-read HttpKernel         $kernel
 * @property-read Configuration      $configuration
 * @property-read Router             $router
 * @property-read Translator         $translator
 *
 * @package Trillium\General
 */
class Application extends Pimple implements HttpKernelInterface, TerminableInterface
{

    /**
     * @var string An application environment
     */
    private $environment = null;

    /**
     * @var boolean Debug mode
     */
    private $isDebug = null;

    /**
     * @var string Locale
     */
    private $locale = 'en';

    /**
     * @var string Path to the root application directory
     */
    private $applicationDir = null;

    /**
     * @var string Path to the cache directory
     */
    private $cacheDir = null;

    /**
     * @var string Path to the logs directory
     */
    private $logsDir = null;

    /**
     * @var string Path to the locales directory
     */
    private $localesDir = null;

    /**
     * Constructor
     *
     * @return self
     */
    public function __construct()
    {
        parent::__construct();

        if ($this->isDebug()) {
            Debug::enable(-1, true);
        }

        $this['dispatcher']    = new EventDispatcher();

        $this['configuration'] = new Configuration($this->getEnvironment());
        $this->setLocale($this->configuration->get('locale', $this->getLocale()));

        $this['logger']        = new Logger('Trillium');
        $this['logger.stream'] = $this->getLogsDir() . $this->getEnvironment() . '.log';
        $this['logger.level']  = $this->isDebug() ? Logger::DEBUG : Logger::ERROR;
        $this->logger->pushHandler(new StreamHandler($this['logger.stream'], $this['logger.level']));

        $this['resolver']      = new ControllerResolver($this->logger, new ControllerFactory($this));
        $this['requestStack']  = new RequestStack();
        $this['kernel']        = new HttpKernel($this->dispatcher, $this->resolver, $this->requestStack);

        $this['router']        = new Router(
            new YamlFileLoader(new FileLocator($this->configuration->getPaths())),
            'routes.yml',
            [
                'cache_dir'             => $this->getCacheDir(),
                'debug'                 => $this->isDebug(),
                'generator_cache_class' => 'CachedUrlGenerator',
                'matcher_cache_class'   => 'CachedUrlMatcher',
            ],
            null,
            $this->logger
        );
        $this->router->getContext()->setHttpPort($this->configuration->get('request.http_port', 80));
        $this->router->getContext()->setHttpsPort($this->configuration->get('request.https_port', 443));

        $this->dispatcher->addSubscriber(new RouterListener($this->router->getMatcher(), null, $this->logger));
        $this->dispatcher->addSubscriber(new LocaleListener($this, $this->requestStack, $this->router->getMatcher()));
        $this->dispatcher->addSubscriber(new ResponseListener($this->configuration->get('charset', 'UTF-8')));
        $this->dispatcher->addSubscriber(new ExceptionListener(new ExceptionController(), $this, $this->logger));

        $this['translator'] = new Translator($this->getLocale());
        $localeFallback = $this->configuration->get('locale_fallback', 'en');
        $this->translator->setFallbackLocales([$localeFallback]);
        $this->translator->addLoader('json', new JsonFileLoader());
        $this->translator->addResource('json', $this->getLocalesDir() . $localeFallback . '.json', $localeFallback);
    }

    /**
     * Gets a parameter or an object.
     *
     * @param string $id The unique identifier
     *
     * @return mixed
     */
    public function __get($id)
    {
        return $this->offsetGet($id);
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        $response = $this->kernel->handle($request, $type, $catch);
        $response->send();

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function terminate(Request $request, Response $response)
    {
        $this->kernel->terminate($request, $response);
    }

    /**
     * Run application
     *
     * Handles a request to convert it to a response
     * Terminates a request/response cycle
     *
     * @param Request $request A request instance
     *
     * @return void
     */
    public function run(Request $request)
    {
        $response = $this->handle($request);
        $this->terminate($request, $response);
    }

    /**
     * Returns an application environment
     *
     * @throws \RuntimeException
     * @return string
     */
    public function getEnvironment()
    {
        if ($this->environment === null) {
            $this->environment = $this->getApplicationDir() . '.environment';
            if (!is_file($this->environment)) {
                throw new \RuntimeException('Environment is not defined');
            }
            $this->environment = file_get_contents($this->environment);
            if (!in_array($this->environment, ['development', 'testing', 'production'])) {
                throw new \RuntimeException(sprintf('Environment "%s" is not available'));
            }
        }

        return $this->environment;
    }

    /**
     * Checks, if debug mode is enabled
     *
     * @return boolean
     */
    public function isDebug()
    {
        if ($this->isDebug === null) {
            $this->isDebug = $this->getEnvironment() !== 'production';
        }

        return $this->isDebug;
    }

    /**
     * Gets an application locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Sets an application locale
     *
     * @param string $locale Locale
     *
     * @return void
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * Returns the path to the root application directory
     *
     * @return string
     */
    public function getApplicationDir()
    {
        if ($this->applicationDir === null) {
            $this->applicationDir = realpath(__DIR__ . '/../../../') . '/';
        }

        return $this->applicationDir;
    }

    /**
     * Returns path to the cache directory
     *
     * @return string
     */
    public function getCacheDir()
    {
        if ($this->cacheDir === null) {
            $this->cacheDir = $this->getApplicationDir() . 'resources/cache/';
        }

        return $this->cacheDir;
    }

    /**
     * Returns path to the logs directory
     *
     * @return string
     */
    public function getLogsDir()
    {
        if ($this->logsDir === null) {
            $this->logsDir = $this->getApplicationDir() . 'resources/logs/';
        }

        return $this->logsDir;
    }

    /**
     * Returns path to the locales directory
     *
     * @return string
     */
    public function getLocalesDir()
    {
        if ($this->localesDir === null) {
            $this->localesDir = $this->getApplicationDir() . 'resources/locales/';
        }

        return $this->localesDir;
    }

}
