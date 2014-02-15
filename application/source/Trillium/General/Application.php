<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\General;

use Pimple;
use Symfony\Component\Debug\DebugClassLoader;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\EventListener\ExceptionListener;
use Symfony\Component\HttpKernel\EventListener\ResponseListener;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;
use Trillium\General\Controller\ControllerFactory;
use Trillium\General\Controller\ControllerResolver;
use Trillium\General\EventListener\LocaleListener;
use Trillium\General\EventListener\RequestListener;
use Trillium\General\Exception\DebugExceptionHandler;
use Trillium\General\Exception\ExceptionHandler;
use Trillium\Provider\ConfigurationProvider;
use Trillium\Provider\LoggerProvider;
use Trillium\Provider\MySQLiProvider;
use Trillium\Provider\RouterProvider;
use Trillium\Provider\SecurityProvider;
use Trillium\Provider\SessionProvider;
use Trillium\Provider\TranslatorProvider;
use Trillium\Provider\TwigProvider;

/**
 * Application Class
 *
 * @property-read \Symfony\Component\EventDispatcher\EventDispatcher $dispatcher
 * @property-read \Monolog\Logger                                    $logger
 * @property-read \Symfony\Component\HttpKernel\HttpKernel           $kernel
 * @property-read \Trillium\Service\Configuration\Configuration      $configuration
 * @property-read \Symfony\Component\Routing\Router                  $router
 * @property-read \Symfony\Component\Translation\Translator          $translator
 * @property-read \Trillium\Service\Twig\TwigEngine                  $view
 * @property-read \Trillium\Service\MySQLi\MySQLi                    $mysqli
 *
 * @package Trillium\General
 */
class Application extends Pimple implements HttpKernelInterface, TerminableInterface
{

    /**
     * Version
     */
    const VERSION = 'dev';

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
     * @var string Path to the views directory
     */
    private $viewsDir = null;

    /**
     * @var string Path to the source assets directory
     */
    private $sourceAssetsDir = null;

    /**
     * @var string Path to the public assets directory
     */
    private $publicAssetsDir = null;

    /**
     * @var boolean Is application booted?
     */
    private $booted = false;

    /**
     * Constructor
     *
     * @return self
     */
    public function __construct()
    {
        parent::__construct();
        error_reporting(-1);
        if ($this->isDebug()) {
            ErrorHandler::register(-1, 1);
            if ('cli' !== php_sapi_name()) {
                DebugExceptionHandler::register();
                // CLI - display errors only if they're not already logged to STDERR
            } elseif ((!ini_get('log_errors') || ini_get('error_log'))) {
                ini_set('display_errors', 1);
            }
            DebugClassLoader::enable();
        }
        $this['configuration'] = (new ConfigurationProvider($this->getEnvironment()))->configuration();
        $this->setLocale($this['configuration']->get('locale', $this->getLocale()));
        $this['logger']        = (new LoggerProvider(
                                    'Trillium',
                                    $this->getLogsDir() . $this->getEnvironment() . '.log',
                                    $this->isDebug()
                                ))->logger();
        $this['router']        = (new RouterProvider(
                                    $this->configuration->getPaths(),
                                    'routes',
                                    $this->configuration->get('request.http_port', 80),
                                    $this->configuration->get('request.https_port', 443),
                                    $this->getCacheDir(),
                                    $this->isDebug(),
                                    $this->logger
                                ))->router();
        $this['dispatcher']    = new EventDispatcher();
        $requestStack          = new RequestStack();
        $this['kernel']        = new HttpKernel(
                                    $this->dispatcher,
                                    new ControllerResolver($this->logger, new ControllerFactory($this)),
                                    $requestStack
                                );
        $this['translator']    = (new TranslatorProvider(
                                    $this->getLocale(),
                                    $this->configuration->get('locale_fallback', 'en'),
                                    $this->getLocalesDir()
                                ))->translator();
        $this['view']          = (new TwigProvider(
                                    $this->getViewsDir(),
                                    $this->isDebug(),
                                    $this->configuration->get('charset', 'UTF-8'),
                                    $this->getCacheDir() . 'twig',
                                    $this->translator,
                                    $this->router->getGenerator()
                                ))->twig();
        $this['mysqli']        = (new MySQLiProvider($this->configuration->load('mysqli', 'yml')->get()))->mysqli();
        $session               = new SessionProvider();
        $security              = new SecurityProvider(
                                    $this->configuration->load('security', 'yml')->get(),
                                    $this->configuration->get('request.http_port', 80),
                                    $this->configuration->get('request.https_port', 443),
                                    $this->kernel,
                                    $this->dispatcher,
                                    $this->router->getGenerator(),
                                    $this->router->getMatcher(),
                                    $this->logger
                                );
        $this['session']       = $session->session();
        $this['security']      = $security->securityContext();
        $this->dispatcher->addSubscriber($session->subscriber());
        $this->dispatcher->addSubscriber(new RouterListener($this->router->getMatcher(), null, $this->logger));
        $this->dispatcher->addSubscriber($security->firewall());
        $this->dispatcher->addSubscriber($security->rememberMeListener());
        $this->dispatcher->addSubscriber(new LocaleListener($this, $requestStack, $this->router->getMatcher()));
        $this->dispatcher->addSubscriber(new ResponseListener($this->configuration->get('charset', 'UTF-8')));
        if (!$this->isDebug()) {
            $this->dispatcher->addSubscriber(new ExceptionListener(new ExceptionHandler($this), $this->logger));
        }
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
     * Boot application
     *
     * @return boolean
     */
    public function boot()
    {
        if ($this->booted) {
            return true;
        }
        // Only for HttpKernelInterface::MASTER_REQUEST
        $this->dispatcher->addSubscriber(new RequestListener($this));

        return true;
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
        $this->boot();
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
            $this->environment = @file_get_contents($this->environment);
            if ($this->environment === false) {
                throw new \RuntimeException(sprintf('Unable to get environment: %s', error_get_last()['message']));
            }
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

    /**
     * Returns path to the views directory
     *
     * @return string
     */
    public function getViewsDir()
    {
        if ($this->viewsDir === null) {
            $this->viewsDir = $this->getApplicationDir() . 'resources/views/';
        }

        return $this->viewsDir;
    }

    /**
     * Returns path to the source assets directory
     *
     * @return string
     */
    public function getSourceAssetsDir()
    {
        if ($this->sourceAssetsDir === null) {
            $this->sourceAssetsDir = $this->getApplicationDir() . 'resources/assets/';
        }

        return $this->sourceAssetsDir;
    }

    /**
     * Returns path to the public assets directory
     *
     * @return string
     */
    public function getPublicAssetsDir()
    {
        if ($this->publicAssetsDir === null) {
            $this->publicAssetsDir = $this->getApplicationDir() . '../public/assets/';
        }

        return $this->publicAssetsDir;
    }

}
