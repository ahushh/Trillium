<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Provider;

use Symfony\Component\Translation\Loader\JsonFileLoader;
use Symfony\Component\Translation\Translator;
use Trillium\General\Application;

/**
 * TranslatorProvider Class
 *
 * @package Trillium\Provider
 */
class TranslatorProvider
{

    /**
     * Create the translator instance
     *
     * @param Application $app An application instance
     *
     * @return Translator
     */
    public function register(Application $app)
    {
        $translator = new Translator($app->getLocale());
        $localeFallback = $app->configuration->get('locale_fallback', 'en');
        $translator->setFallbackLocales([$localeFallback]);
        $translator->addLoader('json', new JsonFileLoader());
        $translator->addResource('json', $app->getLocalesDir() . $localeFallback . '.json', $localeFallback);

        return $translator;
    }

}
