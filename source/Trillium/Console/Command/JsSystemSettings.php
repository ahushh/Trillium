<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Trillium\Console\CommandInterface;
use Trillium\Service\Settings\Settings;
use Vermillion\Container;

/**
 * JsSystemSettings Class
 *
 * @package Trillium\Console\Command
 */
class JsSystemSettings implements CommandInterface
{

    /**
     * Name of the generated file
     */
    const FILENAME = 'settings.js';

    /**
     * @var string A destination directory for a generated script
     */
    private $directory;

    /**
     * @var Settings Settings
     */
    private $settings;

    /**
     * @var Filesystem Filesystem instance
     */
    private $fs;

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $this->directory . self::FILENAME;
        $this->fs->dumpFile(
            $path,
            sprintf('generated.settings=%s;', json_encode($this->settings->get(null, Settings::SYSTEM)))
        );
        $output->writeln($this->fs->exists($path) ? '<fg=green>Success</fg=green>' : '<fg=red>Failed</fg=red>');
    }

    /**
     * {@inheritdoc}
     */
    public function getArguments()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Dump system settings into javascript file';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'jss';
    }

    /**
     * {@inheritdoc}
     */
    public function register(Container $container)
    {
        /** @var $env \Vermillion\Environment */
        $env             = $container['environment'];
        $this->directory = $env->getDirectory('static.generated');
        $this->settings  = $container['settings'];
        $this->fs        = $container['filesystem'];
    }

}
