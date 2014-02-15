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

/**
 * TranslatorProvider Class
 *
 * @package Trillium\Provider
 */
class TranslatorProvider
{

    /**
     * @var Translator Instance of translator
     */
    private $translator;

    /**
     * Constructor
     *
     * @param string $locale           Current locale
     * @param string $localeFallback   Fallback locale
     * @param string $localesDirectory Path to the locales directory
     *
     * @return self
     */
    public function __construct($locale, $localeFallback, $localesDirectory)
    {
        $this->translator = new Translator($locale);
        $this->translator->setFallbackLocales([$localeFallback]);
        $this->translator->addLoader('json', new JsonFileLoader());
        $this->translator->addResource(
            'json',
            $localesDirectory . $localeFallback . '.json',
            $localeFallback
        );
    }

    /**
     * Returns translator instance
     *
     * @return Translator
     */
    public function translator()
    {
        return $this->translator;
    }

}
