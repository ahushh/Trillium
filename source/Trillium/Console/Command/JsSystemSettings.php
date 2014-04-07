<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Trillium\Service\Settings\Settings;

/**
 * JsSystemSettings Class
 *
 * @package Trillium\Console\Command
 */
class JsSystemSettings extends Command
{

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
     * Constructor
     *
     * @param string     $directory A destination directory for a generated script
     * @param Settings   $settings  Settings
     * @param Filesystem $fs        Filesystem instance
     *
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @return self
     */
    public function __construct($directory, Settings $settings, Filesystem $fs)
    {
        if (!is_dir($directory)) {
            throw new \InvalidArgumentException(sprintf('Directory %s does not exists', $directory));
        } elseif (!is_writable($directory)) {
            throw new \InvalidArgumentException(sprintf('Directory %s is not writable', $directory));
        }
        $this->directory = $directory;
        $this->settings  = $settings;
        $this->fs        = $fs;
        parent::__construct('jss');
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Dump system settings into javascript file')
            ->addOption(
                'path',
                'p',
                InputOption::VALUE_OPTIONAL,
                'Destination path to the file',
                $this->directory . 'settings.js'
            );
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getOption('path');
        $this->fs->dumpFile(
            $path,
            sprintf('Trillium.settings.system=%s;', json_encode($this->settings->get(null, Settings::SYSTEM)))
        );
        $output->writeln($this->fs->exists($path) ? '<fg=green>Success</fg=green>' : '<fg=red>Failed</fg=red>');
    }

}
