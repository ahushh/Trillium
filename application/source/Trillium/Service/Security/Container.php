<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Service\Security;

use Symfony\Component\HttpFoundation\RequestMatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Provider\RememberMeAuthenticationProvider;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\User\UserChecker;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Provider\AnonymousAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager;
use Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolver;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler;
use Symfony\Component\Security\Core\Authorization\Voter\RoleHierarchyVoter;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Symfony\Component\Security\Http\Firewall;
use Symfony\Component\Security\Http\Firewall\RememberMeListener;
use Symfony\Component\Security\Http\FirewallMap;
use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;
use Symfony\Component\Security\Http\Firewall\AccessListener;
use Symfony\Component\Security\Http\Firewall\BasicAuthenticationListener;
use Symfony\Component\Security\Http\Firewall\LogoutListener;
use Symfony\Component\Security\Http\Firewall\SwitchUserListener;
use Symfony\Component\Security\Http\Firewall\AnonymousAuthenticationListener;
use Symfony\Component\Security\Http\Firewall\ContextListener;
use Symfony\Component\Security\Http\Firewall\ExceptionListener;
use Symfony\Component\Security\Http\Firewall\ChannelListener;
use Symfony\Component\Security\Http\EntryPoint\FormAuthenticationEntryPoint;
use Symfony\Component\Security\Http\EntryPoint\BasicAuthenticationEntryPoint;
use Symfony\Component\Security\Http\EntryPoint\RetryAuthenticationEntryPoint;
use Symfony\Component\Security\Http\RememberMe\ResponseListener;
use Symfony\Component\Security\Http\RememberMe\TokenBasedRememberMeServices;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategy;
use Symfony\Component\Security\Http\Logout\SessionLogoutHandler;
use Symfony\Component\Security\Http\Logout\DefaultLogoutSuccessHandler;
use Symfony\Component\Security\Http\AccessMap;
use Symfony\Component\Security\Http\HttpUtils;
use Trillium\Service\Security\Provider\AdvancedUserProviderInterface;
use Trillium\Service\Security\Provider\MySQLiUserProvider;

/**
 * Container class
 *
 * Based on Silex Symfony Security component Provider.
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * Remember-me authentication based on Silex Remember-me authentication for the SecurityServiceProvider
 * @author Jérôme Tamarelle <jerome@tamarelle.net>
 *
 * @package Trillium\Service\Security
 */
class Container extends \Pimple
{

    /**
     * Constructor
     *
     * <pre>
     * Values:
     * -----------------------------------------------------------------------------------------------------------------
     * Type                     Key            Description                                            Is required?
     * -----------------------------------------------------------------------------------------------------------------
     * HttpKernelInterface      http_kernel    Symfony Http Kernel                                    yes
     * EventDispatcherInterface dispatcher     Event dispatcher                                       yes
     * LoggerInterface          logger         Logger                                                 no
     * UrlGeneratorInterface    url_generator  Symfony Routing Url Generator                          no
     * UrlMatcherInterface      url_matcher    Symfony Routing Url Matcher                            no
     * mysqli                   mysqli         MySQLi instance. If you want to use MySQLiUserProvider no
     * int                      http_port      HTTP port                                              no
     * int                      https_port     HTTPS port                                             no
     * </pre>
     *
     * Note:
     *
     * You must add the following routes for each firewall, if you want to use form and/or logout:
     *
     * For logout: value of "logout[logout_path]" option (with "GET" request method)
     *
     * For form: value of "form[check_path]" option (with "GET" and/or "POST" request method)
     *
     * To use MySQLiUserProvider you need to specify "mysqli" in users section of the your configuration
     *
     * @see \Symfony\Component\EventDispatcher\EventDispatcherInterface
     * @see \Psr\Log\LoggerInterface
     * @see \Symfony\Component\Routing\Generator\UrlGeneratorInterface
     * @see \Symfony\Component\Routing\Matcher\UrlMatcherInterface
     * @see \Symfony\Component\HttpKernel\HttpKernelInterface
     * @see \mysqli
     *
     * @param array $values Values
     *
     * @return self
     */
    public function __construct(array $values)
    {
        $this['role_hierarchy']      = [];
        $this['access_rules']        = [];
        $this['hide_user_not_found'] = true;
        $this['url_generator']       = null;
        $this['url_matcher']         = null;
        $this['logger']              = null;
        $this['mysqli']              = null;
        $this['http_port']           = 80;
        $this['https_port']          = 443;
        $this['security'] = $this->share(
            function ($c) {
                return new SecurityContext($c['authentication_manager'], $c['access_manager']);
            }
        );
        $this['authentication_manager'] = $this->share(
            function ($c) {
                $manager = new AuthenticationProviderManager($c['authentication_providers']);
                $manager->setEventDispatcher($c['dispatcher']);

                return $manager;
            }
        );
        // by default, all users use the digest encoder
        $this['encoder_factory'] = $this->share(
            function ($c) {
                return new EncoderFactory([
                    'Symfony\Component\Security\Core\User\UserInterface' => $c['encoder.digest'],
                ]);
            }
        );
        $this['encoder.digest'] = $this->share(
            function () {
                return new MessageDigestPasswordEncoder();
            }
        );
        $this['user_checker'] = $this->share(
            function () {
                return new UserChecker();
            }
        );
        $this['access_manager'] = $this->share(
            function ($c) {
                return new AccessDecisionManager($c['voters']);
            }
        );
        $this['voters'] = $this->share(
            function ($c) {
                return [
                    new RoleHierarchyVoter(new RoleHierarchy($c['role_hierarchy'])),
                    new AuthenticatedVoter($c['trust_resolver']),
                ];
            }
        );
        $this['firewall'] = $this->share(
            function ($c) {
                return new Firewall($c['firewall_map'], $c['dispatcher']);
            }
        );
        $this['channel_listener'] = $this->share(
            function ($c) {
                return new ChannelListener(
                    $c['access_map'],
                    new RetryAuthenticationEntryPoint($c['http_port'], $c['https_port']),
                    $c['logger']
                );
            }
        );
        // generate the build-in authentication factories
        foreach (['logout', 'pre_auth', 'form', 'http', 'remember_me', 'anonymous'] as $type) {
            $entryPoint = null;
            if ('http' === $type) {
                $entryPoint = 'http';
            } elseif ('form' === $type) {
                $entryPoint = 'form';
            }
            $this['authentication_listener.factory.' . $type] = $this->protect(
                function ($name, $options) use ($type, $entryPoint) {
                    if ($entryPoint && !isset($this['entry_point.' . $name . '.' . $entryPoint])) {
                        $this['entry_point.' . $name . '.' . $entryPoint]
                            = $this['entry_point.' . $entryPoint . '._proto']($name, $options);
                    }
                    if (!isset($this['authentication_listener.' . $name . '.' . $type])) {
                        $this['authentication_listener.' . $name . '.' . $type]
                            = $this['authentication_listener.' . $type . '._proto']($name, $options);
                    }
                    $provider = 'anonymous' === $type ? 'anonymous' : 'dao';
                    if (!isset($this['authentication_provider.' . $name . '.' . $provider])) {
                        $this['authentication_provider.' . $name . '.' . $provider]
                            = $this['authentication_provider.' . $provider . '._proto']($name);
                    }

                    return [
                        'authentication_provider.' . $name . '.' . $provider,
                        'authentication_listener.' . $name . '.' . $type,
                        $entryPoint ? 'entry_point.' . $name . '.' . $entryPoint : null,
                        $type
                    ];
                }
            );
        }
        $this['firewall_map'] = $this->share(
            function () {
                $positions = ['logout', 'pre_auth', 'form', 'http', 'remember_me', 'anonymous'];
                $providers = [];
                $configs   = [];
                foreach ($this['firewalls'] as $name => $firewall) {
                    $entryPoint = null;
                    $pattern    = isset($firewall['pattern'])   ? $firewall['pattern']          : null;
                    $users      = isset($firewall['users'])     ? $firewall['users']            : [];
                    $security   = isset($firewall['security'])  ? (bool) $firewall['security']  : true;
                    $stateless  = isset($firewall['stateless']) ? (bool) $firewall['stateless'] : false;
                    unset($firewall['pattern'], $firewall['users'], $firewall['security'], $firewall['stateless']);
                    $protected = false === $security ? false : count($firewall);
                    $listeners = ['channel_listener'];
                    if ($protected) {
                        if (!isset($this['context_listener.' . $name])) {
                            if (!isset($this['user_provider.' . $name])) {
                                if (is_array($users)) {
                                    $this['user_provider.' . $name] = $this['user_provider.inmemory._proto']($users);
                                } elseif ($users === 'mysqli') {
                                    $this['user_provider.' . $name] = $this['user_provider.mysqli._proto']();
                                } else {
                                    $this['user_provider.' . $name] = $users;
                                }
                            }
                            $this['context_listener.' . $name] = $this['context_listener._proto'](
                                $name,
                                [$this['user_provider.' . $name]]
                            );
                        }
                        if (false === $stateless) {
                            $listeners[] = 'context_listener.' . $name;
                        }
                        $factories = [];
                        foreach ($positions as $position) {
                            $factories[$position] = [];
                        }
                        foreach ($firewall as $type => $options) {
                            if ('switch_user' === $type) {
                                continue;
                            }
                            // normalize options
                            if (!is_array($options)) {
                                if (!$options) {
                                    continue;
                                }
                                $options = [];
                            }
                            if (!isset($this['authentication_listener.factory.' . $type])) {
                                throw new \LogicException(sprintf(
                                    'The "%s" authentication entry is not registered.', $type
                                ));
                            }
                            list(
                                $providerId,
                                $listenerId,
                                $entryPointId,
                                $position
                            ) = $this['authentication_listener.factory.' . $type]($name, $options);
                            if (null !== $entryPointId) {
                                $entryPoint = $entryPointId;
                            }
                            $factories[$position][] = $listenerId;
                            $providers[] = $providerId;
                        }
                        foreach ($positions as $position) {
                            foreach ($factories[$position] as $listener) {
                                $listeners[] = $listener;
                            }
                        }
                        $listeners[] = 'access_listener';
                        if (isset($firewall['switch_user'])) {
                            $this['switch_user.' . $name] = $this['authentication_listener.switch_user._proto'](
                                $name,
                                $firewall['switch_user']
                            );
                            $listeners[] = 'switch_user.' . $name;
                        }
                        if (!isset($this['exception_listener.' . $name])) {
                            if (null == $entryPoint) {
                                $this[$entryPoint = 'entry_point.' . $name . '.form']
                                    = $this['entry_point.form._proto']($name, []);
                            }
                            $this['exception_listener.' . $name] = $this['exception_listener._proto']($entryPoint, $name);
                        }
                    }
                    $configs[$name] = [$pattern, $listeners, $protected];
                }
                $this['authentication_providers'] = array_map(
                    function ($provider) {
                        return $this[$provider];
                    },
                    array_unique($providers)
                );
                $map = new FirewallMap();
                foreach ($configs as $name => $config) {
                    $map->add(
                        is_string($config[0]) ? new RequestMatcher($config[0]) : $config[0],
                        array_map(
                            function ($listenerId) use ($name) {
                                $listener = $this[$listenerId];
                                if (isset($this['remember_me.service.' . $name])) {
                                    if ($listener instanceof AbstractAuthenticationListener) {
                                        $listener->setRememberMeServices($this['remember_me.service.' . $name]);
                                    }
                                    if ($listener instanceof LogoutListener) {
                                        $listener->addHandler($this['remember_me.service.' . $name]);
                                    }
                                }

                                return $listener;
                            },
                            $config[1]
                        ),
                        $config[2] ? $this['exception_listener.' . $name] : null
                    );
                }

                return $map;
            }
        );
        $this['access_listener'] = $this->share(
            function ($c) {
                return new AccessListener(
                    $c['security'],
                    $c['access_manager'],
                    $c['access_map'],
                    $c['authentication_manager'],
                    $c['logger']
                );
            }
        );
        $this['access_map'] = $this->share(
            function ($c) {
                $map = new AccessMap();
                foreach ($c['access_rules'] as $path => $rule) {
                    $path = new RequestMatcher($path);
                    $map->add($path, $rule['roles'], isset($rule['channel']) ? $rule['channel'] : null);
                }

                return $map;
            }
        );
        $this['trust_resolver'] = $this->share(
            function () {
                return new AuthenticationTrustResolver(
                    'Symfony\Component\Security\Core\Authentication\Token\AnonymousToken',
                    'Symfony\Component\Security\Core\Authentication\Token\RememberMeToken'
                );
            }
        );
        $this['session_strategy'] = $this->share(
            function () {
                return new SessionAuthenticationStrategy('migrate');
            }
        );
        $this['http_utils'] = $this->share(
            function ($c) {
                return new HttpUtils(
                    $c['url_generator'],
                    $c['url_matcher']
                );
            }
        );
        $this['last_error'] = $this->protect(
            function (Request $request) {
                if ($request->attributes->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
                    return $request->attributes->get(SecurityContextInterface::AUTHENTICATION_ERROR)->getMessage();
                }
                $session = $request->getSession();
                if ($session && $session->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
                    $error = $session->get(SecurityContextInterface::AUTHENTICATION_ERROR)->getMessage();
                    $session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);

                    return $error;
                }

                return null;
            }
        );
        // prototypes (used by the Firewall Map)
        $this['context_listener._proto'] = $this->protect(
            function ($providerKey, $userProviders) {
                return $this->share(
                    function () use ($userProviders, $providerKey) {
                        return new ContextListener(
                            $this['security'],
                            $userProviders,
                            $providerKey,
                            $this['logger'],
                            $this['dispatcher']
                        );
                    }
                );
            }
        );
        $this['user_provider.inmemory._proto'] = $this->protect(
            function ($params) {
                return $this->share(
                    function () use ($params) {
                        $users = [];
                        foreach ($params as $name => $user) {
                            $users[$name] = ['roles' => (array) $user[0], 'password' => $user[1]];
                        }

                        return new InMemoryUserProvider($users);
                    }
                );
            }
        );
        $this['user_provider.mysqli._proto'] = $this->protect(
            function () {
                return $this->share(
                    function ($c) {
                        $provider = new MySQLiUserProvider($c['mysqli']);
                        if (!isset($c['user_provider.mysqli.supports_class'])) {
                            $c['user_provider.mysqli.supports_class'] = 'Trillium\Service\Security\User\User';
                        }
                        $provider->setSupportsClass($c['user_provider.mysqli.supports_class']);

                        return $provider;
                    }
                );
            }
        );
        $this['exception_listener._proto'] = $this->protect(
            function ($entryPoint, $name) {
                return $this->share(
                    function () use ($entryPoint, $name) {
                        return new ExceptionListener(
                            $this['security'],
                            $this['trust_resolver'],
                            $this['http_utils'],
                            $name,
                            $this[$entryPoint],
                            null, // errorPage
                            null, // AccessDeniedHandlerInterface
                            $this['logger']
                        );
                    }
                );
            }
        );
        $this['authentication.success_handler._proto'] = $this->protect(
            function ($name, $options) {
                return $this->share(
                    function () use ($name, $options) {
                        $handler = new DefaultAuthenticationSuccessHandler(
                            $this['http_utils'],
                            $options
                        );
                        $handler->setProviderKey($name);

                        return $handler;
                    }
                );
            }
        );
        $this['authentication.failure_handler._proto'] = $this->protect(
            function ($name, $options) {
                return $this->share(
                    function () use ($name, $options) {
                        return new DefaultAuthenticationFailureHandler(
                            $this['http_kernel'],
                            $this['http_utils'],
                            $options,
                            $this['logger']
                        );
                    }
                );
            }
        );
        $this['authentication_listener.form._proto'] = $this->protect(
            function ($name, $options) {
                return $this->share(
                    function () use ($name, $options) {
                        if (isset($options['listener_class'])) {
                            $class = $options['listener_class'];
                        } else {
                            $class = 'Symfony\Component\Security\Http\Firewall\UsernamePasswordFormAuthenticationListener';
                        }
                        if (!isset($this['authentication.success_handler.' . $name])) {
                            $this['authentication.success_handler.' . $name]
                                = $this['authentication.success_handler._proto']($name, $options);
                        }
                        if (!isset($this['authentication.failure_handler.' . $name])) {
                            $this['authentication.failure_handler.' . $name]
                                = $this['authentication.failure_handler._proto']($name, $options);
                        }

                        return new $class(
                            $this['security'],
                            $this['authentication_manager'],
                            isset($this['session_strategy.' . $name])
                                ? $this['session_strategy.' . $name]
                                : $this['session_strategy'],
                            $this['http_utils'],
                            $name,
                            $this['authentication.success_handler.' . $name],
                            $this['authentication.failure_handler.' . $name],
                            $options,
                            $this['logger'],
                            $this['dispatcher'],
                            isset($options['with_csrf']) && $options['with_csrf'] && isset($this['form.csrf_provider'])
                                ? $this['form.csrf_provider']
                                : null
                        );
                    }
                );
            }
        );
        $this['authentication_listener.http._proto'] = $this->protect(
            function ($providerKey, $options) {
                return $this->share(
                    function () use ($providerKey, $options) {
                        return new BasicAuthenticationListener(
                            $this['security'],
                            $this['authentication_manager'],
                            $providerKey,
                            $this['entry_point.' . $providerKey . '.http'],
                            $this['logger']
                        );
                    }
                );
            }
        );
        $this['authentication_listener.anonymous._proto'] = $this->protect(
            function ($providerKey, $options) {
                return $this->share(
                    function () use ($providerKey, $options) {
                        return new AnonymousAuthenticationListener(
                            $this['security'],
                            $providerKey,
                            $this['logger']
                        );
                    }
                );
            }
        );
        $this['authentication.logout_handler._proto'] = $this->protect(
            function ($name, $options) {
                return $this->share(
                    function () use ($name, $options) {
                        return new DefaultLogoutSuccessHandler(
                            $this['http_utils'],
                            isset($options['target_url']) ? $options['target_url'] : '/'
                        );
                    }
                );
            }
        );
        $this['authentication_listener.logout._proto'] = $this->protect(
            function ($name, $options) {
                return $this->share(
                    function () use ($name, $options) {
                        if (!isset($this['authentication.logout_handler.' . $name])) {
                            $this['authentication.logout_handler.' . $name]
                                = $this['authentication.logout_handler._proto']($name, $options);
                        }
                        $listener = new LogoutListener(
                            $this['security'],
                            $this['http_utils'],
                            $this['authentication.logout_handler.' . $name],
                            $options,
                            isset($options['with_csrf']) && $options['with_csrf'] && isset($this['form.csrf_provider'])
                                ? $this['form.csrf_provider'] : null
                        );
                        $listener->addHandler(new SessionLogoutHandler());

                        return $listener;
                    }
                );
            }
        );
        $this['authentication_listener.switch_user._proto'] = $this->protect(
            function ($name, $options) {
                return $this->share(
                    function () use ($name, $options) {
                        return new SwitchUserListener(
                            $this['security'],
                            $this['user_provider.' . $name],
                            $this['user_checker'],
                            $name,
                            $this['access_manager'],
                            $this['logger'],
                            isset($options['parameter']) ? $options['parameter'] : '_switch_user',
                            isset($options['role']) ? $options['role'] : 'ROLE_ALLOWED_TO_SWITCH',
                            $this['dispatcher']
                        );
                    }
                );
            }
        );
        $this['entry_point.form._proto'] = $this->protect(
            function ($name, array $options) {
                return $this->share(
                    function () use ($options) {
                        $loginPath = isset($options['login_path']) ? $options['login_path'] : '/login';
                        $useForward = isset($options['use_forward']) ? $options['use_forward'] : false;

                        return new FormAuthenticationEntryPoint(
                            $this['http_kernel'],
                            $this['http_utils'],
                            $loginPath,
                            $useForward
                        );
                    }
                );
            }
        );
        $this['entry_point.http._proto'] = $this->protect(
            function ($name, array $options) {
                return $this->share(
                    function () use ($name, $options) {
                        return new BasicAuthenticationEntryPoint(
                            isset($options['real_name']) ? $options['real_name'] : 'Secured'
                        );
                    }
                );
            }
        );
        $this['authentication_provider.dao._proto'] = $this->protect(
            function ($name) {
                return $this->share(
                    function () use ($name) {
                        return new DaoAuthenticationProvider(
                            $this['user_provider.' . $name],
                            $this['user_checker'],
                            $name,
                            $this['encoder_factory'],
                            $this['hide_user_not_found']
                        );
                    }
                );
            }
        );
        $this['authentication_provider.anonymous._proto'] = $this->protect(
            function ($name) {
                return $this->share(
                    function () use ($name) {
                        return new AnonymousAuthenticationProvider($name);
                    }
                );
            }
        );
        // Remember-me authentication
        $this['remember_me.response_listener'] = $this->share(
            function () {
                return new ResponseListener();
            }
        );
        $this['authentication_listener.factory.remember_me'] = $this->protect(
            function ($name, $options) {
                if (empty($options['key'])) {
                    $options['key'] = $name;
                }
                if (!isset($this['remember_me.service.' . $name])) {
                    $this['remember_me.service.' . $name]
                        = $this['remember_me.service._proto']($name, $options);
                }
                if (!isset($this['authentication_listener.' . $name . '.remember_me'])) {
                    $this['authentication_listener.' . $name . '.remember_me']
                        = $this['authentication_listener.remember_me._proto']($name, $options);
                }
                if (!isset($this['authentication_provider.' . $name . '.remember_me'])) {
                    $this['authentication_provider.' . $name . '.remember_me']
                        = $this['authentication_provider.remember_me._proto']($name, $options);
                }

                return [
                    'authentication_provider.' . $name . '.remember_me',
                    'authentication_listener.' . $name . '.remember_me',
                    null, // entry point
                    'remember_me'
                ];
            }
        );
        $this['remember_me.service._proto'] = $this->protect(
            function ($providerKey, $options) {
                return $this->share(
                    function () use ($providerKey, $options) {
                        $options = array_replace(
                            [
                                'name'                  => 'REMEMBER_ME',
                                'lifetime'              => 31536000,
                                'path'                  => '/',
                                'domain'                => null,
                                'secure'                => false,
                                'httponly'              => true,
                                'always_remember_me'    => false,
                                'remember_me_parameter' => '_remember_me',
                            ],
                            $options
                        );

                        return new TokenBasedRememberMeServices(
                            [$this['user_provider.' . $providerKey]],
                            $options['key'],
                            $providerKey,
                            $options,
                            $this['logger']
                        );
                    }
                );
            }
        );
        $this['authentication_listener.remember_me._proto'] = $this->protect(
            function ($providerKey) {
                return $this->share(
                    function () use ($providerKey) {
                        return new RememberMeListener(
                            $this['security'],
                            $this['remember_me.service.' . $providerKey],
                            $this['authentication_manager'],
                            $this['logger']
                        );
                    }
                );
            }
        );
        $this['authentication_provider.remember_me._proto'] = $this->protect(
            function ($name, $options) {
                return $this->share(
                    function () use ($name, $options) {
                        return new RememberMeAuthenticationProvider($this['user_checker'], $options['key'], $name);
                    }
                );
            }
        );
        foreach ($values as $key => $value) {
            $this[$key] = $value;
        }
    }

    /**
     * Returns the security context instance
     *
     * @return \Symfony\Component\Security\Core\SecurityContext
     */
    public function getSecurityContext()
    {
        return $this['security'];
    }

    /**
     * Returns the firewall instance
     *
     * @return \Symfony\Component\Security\Http\Firewall
     */
    public function getFirewall()
    {
        return $this['firewall'];
    }

    /**
     * Returns the RememberMe Response listener instance
     *
     * @return \Symfony\Component\Security\Http\RememberMe\ResponseListener
     */
    public function getRememberMeResponseListener()
    {
        return $this['remember_me.response_listener'];
    }

    /**
     * Returns a user provider for firewall
     *
     * @param string $name Firewall name
     *
     * @throws \InvalidArgumentException
     * @return AdvancedUserProviderInterface
     */
    public function getUserProvider($name)
    {
        if (!$this->offsetExists('user_provider.' . $name)) {
            throw new \InvalidArgumentException(sprintf('User provider for "%s" firewall does not exists', $name));
        }
        return $this['user_provider.' . $name];
    }

}
