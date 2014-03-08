<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Provider;

use Symfony\Component\Translation\Loader\JsonFileLoader;
use Trillium\Subscriber\Translator as Subscriber;
use Vermillion\Container;
use Vermillion\Provider\ServiceProviderInterface;
use Vermillion\Provider\SubscriberProviderInterface;

/**
 * Translator Class
 *
 * @package Trillium\Provider
 */
class Translator implements ServiceProviderInterface, SubscriberProviderInterface
{

    /**
     * {@inheritdoc}
     */
    public function registerServices(Container $container)
    {
        $container['translator.locale']     = null;
        $container['translator']            = function ($container) {
            /**
             * @var $conf \Vermillion\Configuration\Configuration
             * @var $env  \Vermillion\Environment
             */
            $conf       = $container['configuration'];
            $env        = $container['environment'];
            $locale     = $container['translator.locale'] ? : $conf->get('locale');
            $translator = new \Symfony\Component\Translation\Translator($locale);
            $translator->setFallbackLocales([$conf->get('locale_fallback')]);
            $translator->addLoader('json', new JsonFileLoader());
            $translator->addResource(
                'json',
                $env->getDirectory('locales') . $conf->get('locale_fallback') . '.json',
                $conf->get('locale_fallback')
            );

            return $translator;
        };
        $container['translator.subscriber'] = function ($container) {
            /**
             * @var $env        \Vermillion\Environment
             * @var $translator \Symfony\Component\Translation\Translator
             */
            $env        = $container['environment'];
            $translator = $container['translator'];

            return new Subscriber(
                $translator,
                $env->getDirectory('locales'),
                'json',
                $translator->getLocale(),
                $container['requestStack'],
                $container['router']
            );
        };
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribers(Container $container)
    {
        return [$container['translator.subscriber']];
    }

}
