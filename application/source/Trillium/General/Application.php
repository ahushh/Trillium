<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\General;

use Symfony\Component\Debug\DebugClassLoader;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\EventListener\ExceptionListener;
use Symfony\Component\HttpKernel\EventListener\ResponseListener;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Trillium\General\Controller\ControllerFactory;
use Trillium\General\Controller\ControllerResolver;
use Trillium\General\EventListener\LocaleListener;
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
 * @package Trillium\General
 */
class Application
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
     * @var array List of the directories
     */
    private $directories = [
        'application'   => '/application',
        'configuration' => '/application/resources/configuration',
        'cache'         => '/application/resources/cache',
        'logs'          => '/application/resources/logs',
        'locales'       => '/application/resources/locales',
        'views'         => '/application/resources/views',
        'assets.source' => '/application/resources/assets',
        'assets.public' => '/public/assets',
    ];

    /**
     * @var \Trillium\Service\Configuration\Configuration
     */
    public $configuration;

    /**
     * @var \Monolog\Logger
     */
    public $logger;

    /**
     * @var \Symfony\Component\Routing\Router
     */
    public $router;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    public $dispatcher;

    /**
     * @var \Symfony\Component\HttpKernel\HttpKernel
     */
    public $kernel;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    public $translator;

    /**
     * @var \Trillium\Service\Twig\TwigEngine
     */
    public $view;

    /**
     * @var \Trillium\Service\MySQLi\MySQLi
     */
    public $mysqli;

    /**
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    public $session;

    /**
     * @var \Symfony\Component\Security\Core\SecurityContext
     */
    public $security;

    /**
     * @var \Trillium\Service\Security\Provider\AdvancedUserProviderInterface
     */
    public $userProvider;

    /**
     * Constructor
     *
     * @throws \RuntimeException
     * @return self
     */
    public function __construct()
    {
        error_reporting(-1);
        // Define directories
        foreach ($this->directories as $key => $directory) {
            $this->directories[$key] = realpath(__DIR__ . '/../../../../' . $directory) . '/';
        }
        // Define environment
        $this->environment = $this->getDirectory('application') . '.environment';
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
        // Debug
        $this->isDebug = $this->environment !== 'production';
        if ($this->isDebug) {
            ErrorHandler::register(-1, 1);
            if ('cli' !== php_sapi_name()) {
                DebugExceptionHandler::register();
                // CLI - display errors only if they're not already logged to STDERR
            } elseif ((!ini_get('log_errors') || ini_get('error_log'))) {
                ini_set('display_errors', 1);
            }
            DebugClassLoader::enable();
        }
        // Register services
        $configurationDirectories = [
            $this->getDirectory('configuration') . $this->environment . '/',
            $this->getDirectory('configuration') . 'default/',
        ];
        $this->configuration = (new ConfigurationProvider(
                                    $configurationDirectories,
                                    'application',
                                    'yml'
                                ))->configuration();
        $this->setLocale($this->configuration->get('locale', $this->getLocale()));
        $this->logger        = (new LoggerProvider(
                                    'Trillium',
                                    $this->getDirectory('logs') . $this->getEnvironment() . '.log',
                                    $this->isDebug()
                                ))->logger();
        $this->router        = (new RouterProvider(
                                    $configurationDirectories,
                                    'routes',
                                    $this->configuration->get('request.http_port'),
                                    $this->configuration->get('request.https_port'),
                                    $this->getDirectory('cache'),
                                    $this->isDebug(),
                                    $this->logger
                                ))->router();
        $this->dispatcher    = new EventDispatcher();
        $requestStack        = new RequestStack();
        $this->kernel        = new HttpKernel(
                                    $this->dispatcher,
                                    new ControllerResolver($this->logger, new ControllerFactory($this)),
                                    $requestStack
                                );
        $this->translator    = (new TranslatorProvider(
                                    $this->getLocale(),
                                    $this->configuration->get('locale_fallback', 'en'),
                                    $this->getDirectory('locales')
                                ))->translator();
        $twig                = (new TwigProvider(
                                    $this->getDirectory('views'),
                                    $this->isDebug(),
                                    $this->configuration->get('charset', 'UTF-8'),
                                    $this->getDirectory('cache') . 'twig',
                                    $this->translator,
                                    $this->router->getGenerator()
                                ));
        $this->view          = $twig->twig();
        $this->mysqli        = (new MySQLiProvider($this->configuration->load('mysqli', 'yml')->get()))->mysqli();
        $session             = new SessionProvider();
        $security            = new SecurityProvider(
                                    $this->configuration->load('security', 'yml')->get(),
                                    $this->configuration->get('request.http_port'),
                                    $this->configuration->get('request.https_port'),
                                    $this->kernel,
                                    $this->dispatcher,
                                    $this->router->getGenerator(),
                                    $this->router->getMatcher(),
                                    $this->mysqli,
                                    $this->logger
                                );
        $this->session       = $session->session();
        $this->security      = $security->securityContext();
        $this->userProvider  = $security->userProvider('secured_area');
        $this->dispatcher->addSubscriber($session->subscriber());
        $this->dispatcher->addSubscriber(new RouterListener($this->router->getMatcher(), null, $this->logger));
        $this->dispatcher->addSubscriber($security->firewall());
        $this->dispatcher->addSubscriber($security->activityListener($this->userProvider));
        $this->dispatcher->addSubscriber($security->rememberMeListener());
        $this->dispatcher->addSubscriber(new LocaleListener($this, $requestStack, $this->router->getMatcher()));
        $this->dispatcher->addSubscriber(new ResponseListener($this->configuration->get('charset')));
        $this->dispatcher->addSubscriber($twig->requestListener());
        if (!$this->isDebug()) {
            $this->dispatcher->addSubscriber(new ExceptionListener(new ExceptionHandler($this), $this->logger));
        }
    }

    /**
     * Run application
     *
     * Handles a request to convert it to a response
     * Sends a response
     * Terminates a request/response cycle
     *
     * @param Request $request A request instance
     *
     * @return void
     */
    public function run(Request $request)
    {
        $response = $this->kernel->handle($request);
        $response->send();
        $this->kernel->terminate($request, $response);
    }

    /**
     * Returns an application environment
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Checks, if debug mode is enabled
     *
     * @return boolean
     */
    public function isDebug()
    {
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
     * Returns the path to the directory by key
     *
     * @param string $directory Key
     *
     * @throws \InvalidArgumentException
     * @return string
     */
    public function getDirectory($directory)
    {
        if (!array_key_exists($directory, $this->directories)) {
            throw new \InvalidArgumentException(sprintf('Directory "%s" is not defined', $directory));
        }

        return $this->directories[$directory];
    }

}
