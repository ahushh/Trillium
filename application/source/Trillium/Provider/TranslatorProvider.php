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
     * @var string Current locale
     */
    private $locale;

    /**
     * @var string Fallback locale
     */
    private $localeFallback;

    /**
     * @var string Path to the locales directory
     */
    private $localesDirectory;

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
        $this->locale           = $locale;
        $this->localeFallback   = $localeFallback;
        $this->localesDirectory = $localesDirectory;
    }

    /**
     * Returns translator instance
     *
     * @return Translator
     */
    public function translator()
    {
        if ($this->translator === null) {
            $this->translator = new Translator($this->locale);
            $this->translator->setFallbackLocales([$this->localeFallback]);
            $this->translator->addLoader('json', new JsonFileLoader());
            $this->translator->addResource(
                'json',
                $this->localesDirectory . $this->localeFallback . '.json',
                $this->localeFallback
            );
        }

        return $this->translator;
    }

}
