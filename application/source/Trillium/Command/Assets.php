<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Command;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Filter\Yui\CssCompressorFilter;
use Assetic\Filter\Yui\JsCompressorFilter;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Trillium\General\Console\Command;

/**
 * Assets Class
 *
 * @package Trillium\Command
 */
class Assets extends Command
{

    /**
     * @var array Assets configuration
     */
    private $conf;

    /**
     * @var array Filters configuration (from file)
     */
    private $confFilters;

    /**
     * @var array Loaded filters
     */
    private $filters = [];

    /**
     * @var array Default filters configuration
     */
    private $filtersConf = [
        'global'             => [
            'yui-path'  => null,
            'java-path' => '/usr/bin/java',
        ],
        'yui-js-compressor'  => [
            'charset'              => null,
            'linebreak'            => null,
            'stack-size'           => null,
            'nomunge'              => null,
            'disable-optimization' => null,
            'preserve-semi'        => null,
        ],
        'yui-css-compressor' => [
            'charset'    => null,
            'linebreak'  => null,
            'stack-size' => null,
        ],
    ];

    /**
     * @var array Output messages
     */
    private $messages = [
        'wrong_ignore_value' => '<fg=red>[EE]</fg=red> Wrong ignore value given',
        'invalid_src_dir'    => '<fg=red>[EE]</fg=red> Source directory does not exists',
        'invalid_pub_dir'    => '<fg=red>[EE]</fg=red> Public directory does not exists',
        'ignore'             => '<info>Ignore %s</info>',
        'src_dir'            => '<info>[OK]</info> Source directory: %s',
        'pub_dir'            => '<info>[OK]</info> Public directory: %s',
        'build'              => "\nWill now build...",
        'assets_type'        => "\nType: %s",
        'not_found'          => '<fg=red>[WW]</fg=red> No assets found.',
        'found'              => '%s assets found',
        'overwrite_asset'    => "\t<fg=red>[WW]</fg=red> Overwrite \"%s\" by \"%s\"",
        'found_asset'        => "\tFound: %s \"%s\" with \"%s\" priority",
        'dump_assets'        => 'Dump "%s" into "%s"... ',
        'dump_success'       => '<info>[OK]</info>',
        'dump_failed'        => '<fg=red>[FAIL]</fg=red>',
        'success'            => '<info>Success</info>',
        'failed'             => '<fg=red>Nothing to build</fg=red>',
    ];

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('assets')
            ->setDescription('Build assets via assetic')
            ->addOption(
                'ignore',
                'i',
                InputOption::VALUE_OPTIONAL,
                'Ignore files.' . "\n"
                . '"js" for javascript files' . "\n"
                . '"css" for css file'
            )
            ->addOption(
                'javascript',
                'js',
                InputOption::VALUE_OPTIONAL,
                'Javascript result filename',
                'scripts.js'
            )
            ->addOption(
                'stylesheet',
                'css',
                InputOption::VALUE_OPTIONAL,
                'Styles result filename',
                'styles.css'
            )
        ;
        // Load filters configuration
        $this->conf = $this->app->configuration->load('assets', 'yml')->get();
        if (isset($this->conf['filters'])) {
            $this->confFilters = $this->conf['filters'];
            foreach ($this->filtersConf as $key => $item) {
                // Configuration for a filter is missing
                if (!array_key_exists($key, $this->confFilters)) {
                    $this->confFilters[$key] = $item;
                } elseif (is_array($this->confFilters[$key])) {
                    foreach ($this->filtersConf[$key] as $name => $value) {
                        // Option for a filter configuration is missing
                        if (!array_key_exists($name, $this->confFilters[$key])) {
                            $this->confFilters[$key][$name] = $value;
                        }
                    }
                } else {
                    throw new \LogicException('Unable to read the configuration file');
                }
            }
            unset($this->conf['filters']);
        } else {
            $this->confFilters = $this->filtersConf;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $ignore = $input->getOption('ignore');
        $names  = [
            'css' => $input->getOption('stylesheet'),
            'js'  => $input->getOption('javascript'),
        ];
        $source = realpath($this->app->getSourceAssetsDir()) . '/';
        $public = realpath($this->app->getPublicAssetsDir()) . '/';
        $errors = [];
        if ($ignore !== null && !in_array($ignore, ['css', 'js'])) {
            $errors[] = $this->messages['wrong_ignore_value'];
        }
        if ($source === false) {
            $errors[] = $this->messages['invalid_src_dir'];
        }
        if ($public === false) {
            $errors[] = $this->messages['invalid_pub_dir'];
        }
        if (!empty($errors)) {
            $output->writeln($errors);

            return 1;
        }
        if ($ignore !== null) {
            $output->writeln(sprintf($this->messages['ignore'], $ignore));
        }
        $output->writeln([
            sprintf($this->messages['src_dir'], $source),
            sprintf($this->messages['pub_dir'], $public),
            $this->messages['build']
        ]);
        $assets     = $ignore === null ? ['js', 'css'] : ($ignore === 'css' ? ['js'] : ['css']);
        $i          = 0;
        $filesystem = new Filesystem();
        /**
         * @var $sorted FileAsset[]
         * @var $file   \Symfony\Component\Finder\SplFileInfo
         */
        foreach ($assets as $type) {
            $output->writeln(sprintf($this->messages['assets_type'], $type));
            $collection = [];
            $sorted     = [];
            $iterator   = $this->getIterator('*.' . $type, $source);
            $total      = iterator_count($iterator);
            if ($total === 0) {
                $output->writeln($this->messages['not_found']);
                continue;
            }
            $output->writeln(sprintf($this->messages['found'], $total));
            $a = 1;
            foreach ($iterator as $file) {
                $path               = $file->getRealPath();
                $key                = str_replace($source, '', $path);
                $options            = isset($this->conf[$key])    ? $this->conf[$key]          : [];
                $priority           = isset($options['priority']) ? (int) $options['priority'] : null;
                $options['filters'] = isset($options['filters'])  ? $options['filters']        : [];
                $filters = [];
                if (is_array($options['filters'])) {
                    foreach ($options['filters'] as $filter) {
                        $filters[] = $this->getFilterByAlias($filter);
                    }
                } elseif (!empty($options['filters'])) {
                    $filters[] = $this->getFilterByAlias($options['filters']);
                }
                $asset = new FileAsset($path, $filters);
                if ($output->getVerbosity() === OutputInterface::VERBOSITY_VERBOSE) {
                    $output->writeln(sprintf(
                        $this->messages['found_asset'],
                        $a . '/' . $total,
                        $key,
                        $priority !== null ? $priority : 'unspecified'
                    ));
                }
                if ($priority !== null) {
                    if (isset($sorted[$priority])) {
                        $sourceKey = str_replace($source, '', $sorted[$priority]->getSourceRoot())
                                   . '/'. $sorted[$priority]->getSourcePath();
                        $output->writeln(sprintf(
                            $this->messages['overwrite_asset'],
                            $sourceKey,
                            $key
                        ));
                    }
                    $sorted[$priority] = $asset;
                } else {
                    $collection[] = $asset;
                }
                $a++;
            }
            ksort($sorted);
            $collection     = array_merge($sorted, $collection);
            $collection     = new AssetCollection($collection);
            $collectionPath = $public . $names[$type];
            $output->write(sprintf($this->messages['dump_assets'], $type, $collectionPath));
            $filesystem->dumpFile($collectionPath, $collection->dump());
            if (is_file($collectionPath)) {
                $output->writeln($this->messages['dump_success']);
            } else {
                $output->writeln($this->messages['dump_failed']);
            }
            $i++;
        }
        $output->writeln($this->messages[$i === 0 ? 'failed' : 'success']);

        return 0;
    }

    /**
     * Returns iterator
     *
     * @param string $name      Name
     * @param string $directory Path to a directory
     *
     * @return Finder
     */
    private function getIterator($name, $directory)
    {
        return (new Finder())->files()->name($name)->in($directory);
    }

    /**
     * Returns a filter by an alias
     *
     * @param string $alias An alias
     *
     * @throws \RuntimeException Wrong configuration given
     * @throws \LogicException   Filter does not exists
     * @return CssCompressorFilter
     */
    private function getFilterByAlias($alias)
    {
        if (isset($this->filters[$alias])) {
            return $this->filters[$alias];
        }
        $conf = $this->confFilters[$alias];
        switch ($alias) {
            case 'yui-js-compressor':
                $compressor = new JsCompressorFilter(
                    $this->confFilters['global']['yui-path'],
                    $this->confFilters['global']['java-path']
                );
                $compressor->setCharset($conf['charset']);
                $compressor->setLineBreak($conf['linebreak']);
                $compressor->setStackSize($conf['stack-size']);
                $compressor->setNomunge($conf['nomunge']);
                $compressor->setDisableOptimizations($conf['disable-optimization']);
                $compressor->setPreserveSemi($conf['preserve-semi']);
                $this->filters[$alias] = $compressor;
                break;
            case 'yui-css-compressor':
                $compressor = new CssCompressorFilter(
                    $this->confFilters['global']['yui-path'],
                    $this->confFilters['global']['java-path']
                );
                $compressor->setCharset($conf['charset']);
                $compressor->setLineBreak($conf['linebreak']);
                $compressor->setStackSize($conf['stack-size']);
                $this->filters[$alias] = $compressor;
                break;
            default:
                throw new \LogicException(sprintf('Filter "%s" does not exists', $alias));
        }

        return $this->filters[$alias];
    }

}
