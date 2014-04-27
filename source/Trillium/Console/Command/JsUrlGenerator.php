<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\Route;
use Trillium\Console\CommandInterface;
use Vermillion\Container;

/**
 * JsUrlGenerator Class
 *
 * @package Trillium\Console\Command
 */
class JsUrlGenerator implements CommandInterface
{

    /**
     * Name of the generated file
     */
    const FILENAME = 'url-generator.js';

    /**
     * @var Filesystem Filesystem instance
     */
    private $fs;

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
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $path      = $this->directory . self::FILENAME;
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
        $content    = sprintf(
            'generated.basePath = \'%s\';generated.routes = %s;',
            $basePath,
            json_encode($result)
        );
        $this->fs->dumpFile($path, $content);
        $output->writeln($this->messages[is_file($path) ? 'success' : 'failed']);

        return 0;
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
        return [
            'base-path' => [
                'shortcut'    => null,
                'mode'        => InputOption::VALUE_OPTIONAL,
                'description' => 'Base path to the public directory',
                'default'     => '',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Generate the javascript url generator';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'jug';
    }

    /**
     * {@inheritdoc}
     */
    public function register(Container $container)
    {
        /**
         * @var $env    \Vermillion\Environment
         * @var $router \Symfony\Component\Routing\Router
         */
        $env             = $container['environment'];
        $router          = $container['router'];
        $this->directory = $env->getDirectory('static.generated');
        $this->routes    = $router->getRouteCollection()->all();
        $this->fs        = $container['filesystem'];
    }
}
