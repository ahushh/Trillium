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
use Symfony\Component\Routing\Route;

/**
 * JsUrlGenerator Class
 *
 * @package Trillium\Console\Command
 */
class JsUrlGenerator extends Command
{

    /**
     * @var array Output messages
     */
    private $messages = [
        'invalid_dir'        => '<fg=red>[EE]</fg=red> Directory "%s" does not exists.',
        'permissions_denied' => '<fg=red>[EE]</fg=red> Unable to write to directory "%s". Permissions denied.',
        'overwrite'          => '<fg=red>[WW]</fg=red> Overwrite file "%s"... ',
        'create'             => 'Create file "%s"... ',
        'success'            => '<fg=green>[OK]</fg=green>',
        'failed'             => '<fg=red>[FAILED]</fg=red>',
    ];

    /**
     * @var string A destination directory for a generated script
     */
    private $directory;

    /**
     * @var Route[]
     */
    private $routes;

    /**
     * Constructor
     *
     * @param string  $directory A destination directory for a generated script
     * @param Route[] $routes    Routes
     *
     * @return self
     */
    public function __construct($directory, array $routes)
    {
        $this->directory = $directory;
        $this->routes    = $routes;
        parent::__construct('jug');
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('jug')
            ->setDescription('Generate the javascript url generator')
            ->addOption(
                'path',
                'p',
                InputOption::VALUE_OPTIONAL,
                'Destination path to the file',
                $this->directory . 'application/js/url-generator.js'
            )
            ->addOption(
                'base-path',
                null,
                InputOption::VALUE_OPTIONAL,
                'Base path to the public directory',
                ''
            );
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $path      = $input->getOption('path');
        $basePath  = $input->getOption('base-path');
        $directory = dirname($path);
        $errors    = [];
        if (!is_dir($directory)) {
            $errors[] = sprintf($this->messages['invalid_dir'], $directory);
        } elseif (!is_writable($directory)) {
            $errors[] = sprintf(sprintf($this->messages['permissions_denied'], $directory));
        }
        if (!empty($errors)) {
            $output->writeln($errors);

            return 1;
        }
        $outputPath = str_replace($this->directory, '', $path);
        $output->write(sprintf($this->messages[is_file($path) ? 'overwrite' : 'create'], $outputPath));
        $result = [];
        foreach ($this->routes as $name => $route) {
            $result[$name] = [
                'path'         => $route->getPath(),
                'requirements' => $route->getRequirements(),
                'defaults'     => $route->getDefaults(),
                'variables'    => $route->compile()->getVariables(),
            ];
            unset(
            $result[$name]['defaults']['_controller'],
            $result[$name]['defaults']['_action'],
            $result[$name]['requirements']['_method']
            );
        }
        $filesystem = new Filesystem();
        $content    = sprintf(
            'Trillium.urlGenerator.routes = %s;%sTrillium.urlGenerator.basePath = \'%s\';',
            json_encode($result),
            "\n",
            $basePath
        );
        $filesystem->dumpFile($path, $content);
        $output->writeln($this->messages[is_file($path) ? 'success' : 'failed']);

        return 0;
    }

}
