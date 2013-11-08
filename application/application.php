<?php

/**
 * Part of the Trillium
 *
 * @package Trillium
 */

use Assetic\AssetManager;
use Igorw\Silex\ConfigServiceProvider;
use Kilte\View\ViewServiceProvider;
use Monolog\Logger;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use SilexAssetic\AsseticServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;
use Trillium\Controller\ControllerProvider;
use Trillium\ImageBoard\ImageBoardServiceProvider;
use Trillium\MobileDetect\MobileDetectServiceProvider;
use Trillium\Model\ModelServiceProvider;
use Trillium\Silex\Application;
use Trillium\User\UserServiceProvider;

$app = new Application;

/** Configuration */
$app->register(new ConfigServiceProvider(CONFIG_DIR . 'silex.php'));
$app->register(new ConfigServiceProvider(CONFIG_DIR . 'mysqli.php'));
$app->register(new ConfigServiceProvider(CONFIG_DIR . 'user.php'));
$app->register(new ConfigServiceProvider(CONFIG_DIR . 'trillium.php'));

$app->register(new MonologServiceProvider, [
    'monolog.logfile' => LOGS_DIR . TRILLIUM_ENVIRONMENT . '.log',
    'monolog.name' => 'trillium'
]);

$app->register(new UrlGeneratorServiceProvider);
$app->register(new ModelServiceProvider);
$app->register(new SessionServiceProvider);
$app->register(new UserServiceProvider);

$app->register(new SecurityServiceProvider, [
    'security.firewalls' => [
        'panel' => [
            'pattern' => '^/panel',
            'form'    => [
                'login_path' => '/login',
                'check_path' => '/panel/login_check',
            ],
            'logout' => ['logout_path' => '/panel/logout'],
            'users'   => $app->share(function ($app) {
                return $app['user.manager'];
            }),
        ],
    ],
    'security.role_hierarchy' => [
        'ROLE_ROOT' => $app['user.roles']
    ],
    'security.access_rules' => [
        ['^/panel/mainpage', 'ROLE_ADMIN'],
        ['^/panel/users', 'ROLE_ADMIN'],
        ['^/panel/boards', 'ROLE_ADMIN'],
    ],
]);

/** Imageboard */
$app->register(new ImageBoardServiceProvider);

/** Controllers */
$app->register(new ServiceControllerServiceProvider);
$app->mount('/', new ControllerProvider);

/** Translation */
$app->register(new TranslationServiceProvider, array('locale_fallbacks' => ['en'],));
$app['translator'] = $app->share($app->extend('translator', function(Translator $translator) {
    $translator->addLoader('yaml', new YamlFileLoader());
    $locales = scandir(LOCALES_DIR);
    $locales = array_diff($locales, ['.', '..']);
    foreach ($locales as $locale) {
        $locale = strtolower($locale);
        $translator->addResource('yaml', LOCALES_DIR . $locale, str_replace('.yml', '', $locale));
    }
    return $translator;
}));

/** Markdown */
$app->register(new SilexMarkdown\MarkdownExtension(), ['markdown.features' => ['no_html' => true,],]);

/** MobileDetect */
if (isset($_COOKIE['version']) && in_array($_COOKIE['version'], ['desktop', 'mobile'])) {
    $app['trillium.viewsSet'] = $_COOKIE['version'];
} else {
    $app->register(new MobileDetectServiceProvider);
    $app['trillium.viewsSet'] = $app['mobiledetect.version'] === null ? 'desktop' : 'mobile';
    setcookie('version', $app['trillium.viewsSet'], time() + 86400 * 365, '/', '.' . $_SERVER['SERVER_NAME']);
}

/** Views */
$app->register(new ViewServiceProvider, ['view.path' => VIEWS_DIR . $app['trillium.viewsSet'] . DS]);

/** Macroses for the views */
$app->viewMacros('__', function ($id, array $parameters = array(), $domain = null, $locale = null) use ($app) {
    return $app->trans($id, $parameters, $domain, $locale);
});
$app->viewMacros('url', function ($route, $parameters = array()) use ($app) {
    return $app->url($route, $parameters);
});
$app->viewMacros('escape', function ($string, $quotes = ENT_QUOTES, $charset = null, $doubleEncode = true) use ($app) {
    return $app->escape($string, $quotes, ($charset ?: $app['charset']), $doubleEncode);
});
$app->viewMacros('isGranted', function ($role) use ($app) {
    /** @var SecurityContext $security */
    $security = $app['security'];
    return $security->isGranted($role);
});
$app->viewMacros('assets', function ($path) use ($app) {
    return 'http://' . $_SERVER['SERVER_NAME'] . '/assets/' . $app['trillium.viewsSet'] . '/' . $path;
});

/** Assets */
$app->register(new AsseticServiceProvider);
$app['assetic.path_to_web'] = ASSETS_WEB_DIR;
$app['assetic.options'] = array(
    'debug' => $app['debug'],
    'formulae_cache_dir' => null,
    'auto_dump_assets' => $app['debug'],
);
$app['assetic.asset_manager'] = $app->share(
    $app->extend('assetic.asset_manager', function(AssetManager $am) use ($app) {
        $am->set('styles', new Assetic\Asset\AssetCache(
            new Assetic\Asset\GlobAsset(RESOURCES_DIR . $app['trillium.viewsSet'] . DS . 'css' . DS . '*.css'),
            new Assetic\Cache\FilesystemCache(CACHE_DIR . 'assetic')
        ));
        $am->get('styles')->setTargetPath($app['trillium.viewsSet'] . '/css/styles.css');
        $am->set('scripts', new Assetic\Asset\AssetCache(
            new Assetic\Asset\GlobAsset(RESOURCES_DIR . $app['trillium.viewsSet'] . DS . 'js' . DS . '*.js'),
            new Assetic\Cache\FilesystemCache(CACHE_DIR . 'assetic')
        ));
        $am->get('scripts')->setTargetPath($app['trillium.viewsSet'] . '/js/scripts.js');
        return $am;
    })
);

/** Custom errors handler */
$app->error(function (\Exception $exception, $code) use ($app) {
    if ($app['debug']) {
        return null;
    }
    if ($code !== 404 && $code !== 403) {
        $app->log($exception->getMessage(), [], Logger::ERROR);
    }
    return new Response($app->view(
        'error/' . (in_array($code, [403, 404, 500]) ? $code : 'default'),
        ['message' => ($code === 403 || $code === 404 ? $exception->getMessage() : '')]
    ));
});

/** Insert views to the layout */
$app->after(function (Request $request, Response $response) use ($app) {
    if (strpos($response->headers->get('Content-Type'), 'text/html') !== false) {
        $response->setContent($app->view('layout', [
            'title' => $app['trillium.pageTitle'],
            'boards' => $app->ibBoard()->getList(),
            'content' => $response->getContent()
        ]));
    }
});

$app->run();