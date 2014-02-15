<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Trillium\General\Console\Command;

/**
 * JsUrlGenerator Class
 *
 * @package Trillium\Command
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
                $this->app->getDirectory('assets.source') . 'application/url-generator.js'
            )
            ->addOption(
                'base-path',
                null,
                InputOption::VALUE_OPTIONAL,
                'Base path to the public directory',
                ''
            )
        ;
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
        $outputPath = str_replace($this->app->getDirectory('assets.source'), '', $path);
        $output->write(sprintf($this->messages[is_file($path) ? 'overwrite' : 'create'], $outputPath));
        $routes = $this->app->router->getRouteCollection()->all();
        $result = [];
        foreach ($routes as $name => $route) {
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
        $filesystem->dumpFile($path, $this->getContent(json_encode($result), $basePath));
        $output->writeln($this->messages[is_file($path) ? 'success' : 'failed']);

        return 0;
    }

    /**
     * Returns the content of the JavaScript UrlGenerator
     *
     * Based on the JavascriptRoutingServiceProvider
     * @link https://github.com/RafalFilipek/JavascriptRoutingServiceProvider
     * @author Rafał Filipek <rafal.filipek@gmail.com>
     *
     * @param string $routes   List of the routes
     * @param string $basePath Base path to the public directory
     *
     * @return string
     */
    private function getContent($routes, $basePath)
    {
        return <<<JS
Trillium.urlGenerator = {
    routes: {$routes},
    basePath: '{$basePath}',
    generate: function (name, params) {
        if (this.routes[name]) {
            params = params == undefined ? {} : params;
            var route        = this.routes[name],
                requirements = route.requirements,
                defaults     = route.defaults,
                variables    = route.variables,
                result       = route.path,
                val;
            for (var param in variables) {
                param = variables[param];
                val = params[param] ? params[param] : defaults[param];
                if (val === undefined) {
                    throw 'Missing "' + param + '" parameter for route "'+name+'"!';
                }
                if (requirements.hasOwnProperty(param) && !new RegExp(requirements[param]).test(val)) {
                    throw 'Parameter "' + param + '" for route "' + name + '" must pass "' + requirements[param] + '" test!';
                }
                result = result.replace('{' + param + '}', val);
            }

            return (window.location.protocol + '//' + window.location.hostname + this.basePath + result).replace(/\/$/, '');
        } else {
            throw 'Undefined route "' + name + '"!';
        }
    }
};
JS;

    }

}
