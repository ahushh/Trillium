<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

namespace Trillium\Console\Command;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Filter\FilterInterface;
use Assetic\Filter\Yui\CssCompressorFilter;
use Assetic\Filter\Yui\JsCompressorFilter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Assets Class
 *
 * @package Trillium\Console\Command
 */
class Assets extends Command
{

    /**
     * Checksums filename
     */
    const CHECKSUMS = 'checksums.json';

    /**
     * @var array List of the directories
     */
    private $directories = [
        'source' => false,
        'cache'  => false,
        'public' => false,
    ];

    /**
     * @var array Loaded filters
     */
    private $filters = [];

    /**
     * @var array Assets configuration
     */
    private $configAssets;

    /**
     * @var array Filters configuration
     */
    private $configFilters;

    /**
     * @var Filesystem Filesystem instance
     */
    private $fs;

    /**
     * @var array Checksums
     */
    private $checksums = [
        'current' => [],
        'new'     => [],
    ];

    private $messages = [
        'directory'       => 'Directory [%s]: %s',
        'remove_cache'    => 'Remove cache',
        'collection'      => "\nCollection: %s",
        'fail'            => '<fg=red>[FAIL]</fg=red> %s',
        'non_exists_file' => 'File "%s" does not exists',
        'asset'           => "\tAsset: %s",
        'dump'            => 'Dump collection into %s ... ',
        'success'         => '<fg=green>[OK]</fg=green>',
        'failed'          => '<fg=red>[FAIL]</fg=red>',
        'elapsed_time'    => 'Elapsed time (seconds): %s',
    ];

    /**
     * Constructor
     *
     * @param string $source Source directory
     * @param string $public Public directory
     * @param string $cache  Cache directory
     * @param array  $conf   Configuration
     *
     * @throws \LogicException
     * @return self
     */
    public function __construct($source, $public, $cache, array $conf)
    {
        if (isset($conf['filters'])) {
            $filtersConfig = $conf['filters'];
            unset($conf['filters']);
        } else {
            $filtersConfig = [];
        }
        $this->configFilters        = $this->getFiltersConfig($filtersConfig);
        $this->configAssets         = $this->getAssetsConfig($conf);
        $this->directories          = $this->checkDirectories($source, $public, $cache);
        $this->fs                   = new Filesystem();
        $this->checksums['current'] = $this->loadChecksums();
        parent::__construct('assets');
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Build assets via assetic')
            ->addOption('update-cache', 'u', InputOption::VALUE_NONE, 'Update cache');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $startTime = time();
        foreach ($this->directories as $dirType => $dirPath) {
            $output->writeln(sprintf($this->messages['directory'], $dirType, $dirPath));
        }
        if ($input->getOption('update-cache')) {
            $output->writeln($this->messages['remove_cache']);
            $this->removeCache();
        }
        foreach ($this->configAssets as $collectionName => $values) {
            $output->writeln(sprintf($this->messages['collection'], $collectionName));
            $collection = [];
            foreach ($values['set'] as $items) {
                $name = is_array($items) ? (isset($items[0]) ? trim($items[0]) : '') : $items;
                if (empty($name)) {
                    throw new \LogicException('Filename could not be empty');
                }
                $name = strtr($name, ['\\' => DIRECTORY_SEPARATOR, '/' => DIRECTORY_SEPARATOR]);
                /** @var $files SplFileInfo[] */
                try {
                    if ($name{strlen($name) - 1} == DIRECTORY_SEPARATOR) {
                        $files = (new Finder())->files()->name($values['type'])->in(
                            $this->directories['source'] . $name
                        );
                    } else {
                        $files = [new SplFileInfo($this->directories['source'] . $name, $name, null)];
                    }
                } catch (\Exception $e) {
                    $output->writeln(sprintf($this->messages['fail'], $e->getMessage()));

                    return 1;
                }
                foreach ($files as $file) {
                    if (!$file->isReadable()) {
                        $output->writeln(
                            sprintf(
                                $this->messages['fail'],
                                sprintf($this->messages['non_exists_file'], $file->getRealPath())
                            )
                        );

                        return 1;
                    }
                    $output->writeln(
                        sprintf(
                            $this->messages['asset'],
                            str_replace($this->directories['source'], '', $file->getRealPath())
                        )
                    );
                    $filters      = is_array($items) && isset($items[1]) ? $items[1] : $values['default_filters'];
                    $collection[] = $this->createAsset($file, $filters);
                }
            }
            $collectionPath = $this->directories['public'] . $collectionName;
            $output->write(sprintf($this->messages['dump'], $collectionPath));
            $collection = new AssetCollection($collection);
            $this->fs->dumpFile($collectionPath, $collection->dump());
            $output->writeln($this->messages[$this->fs->exists($collectionPath) ? 'success' : 'failed']);
        }
        $this->fs->dumpFile($this->directories['cache'] . self::CHECKSUMS, json_encode($this->checksums['new']));
        $output->writeln(sprintf($this->messages['elapsed_time'], time() - $startTime));

        return 0;
    }

    /**
     * Creates a asset
     *
     * @param SplFileInfo  $file
     * @param array|string $filters
     *
     * @return FileAsset
     */
    private function createAsset(SplFileInfo $file, $filters = [])
    {
        $baseName     = $file->getBasename();
        $realPath     = $file->getRealPath();
        $hash         = md5_file($realPath);
        $cachedPath   = sprintf(
            '%s' . strtr($file->getRelativePath(), ['\\' => '_', '/' => '_']) . '_%s',
            $this->directories['cache'],
            $baseName
        );
        $cacheExpired = !isset($this->checksums['current'][$realPath]) || $this->checksums['current'][$realPath] != $hash;
        if (!is_file($cachedPath) || $cacheExpired) {
            // Cache expired
            $filters = $this->getFiltersByAliases($filters);
            $source  = $realPath;
            $cached  = false;
        } else {
            // Load from cache
            $filters = [];
            $source  = $cachedPath;
            $cached  = true;
        }
        $this->checksums['new'][$realPath] = $hash;
        $asset                             = new FileAsset($source, $filters);
        if ($cached !== true) {
            $this->fs->dumpFile($cachedPath, $asset->dump());
        }

        return $asset;
    }

    /**
     * Returns checksums
     *
     * @throws \RuntimeException
     * @return array
     */
    private function loadChecksums()
    {
        $checksums = [];
        $filename  = $this->directories['cache'] . self::CHECKSUMS;
        if (is_file($filename)) {
            $checksumsRaw = @file_get_contents($filename);
            if ($checksumsRaw !== false) {
                $checksums = json_decode($checksumsRaw, true);
            } else {
                throw new \RuntimeException(
                    sprintf('Unable to load checksums. An error has occurred: %s', error_get_last()['message'])
                );
            }
        }

        return $checksums;
    }

    /**
     * Removes cache
     *
     * @return void
     */
    private function removeCache()
    {
        $this->fs->remove(
            array_map(
                function ($name) {
                    return $this->directories['cache'] . $name;
                },
                array_diff(scandir($this->directories['cache']), ['.', '..', '.gitignore'])
            )
        );
    }

    /**
     * Returns a filter by an alias
     *
     * @param string $alias An alias
     *
     * @throws \LogicException Filter does not exists
     * @return FilterInterface
     */
    private function getFilter($alias)
    {
        if (isset($this->filters[$alias])) {
            return $this->filters[$alias];
        }
        $conf = $this->configFilters[$alias];
        switch ($alias) {
            case 'yui-js-compressor':
                $compressor = new JsCompressorFilter(
                    $this->configFilters['global']['yui-path'],
                    $this->configFilters['global']['java-path']
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
                    $this->configFilters['global']['yui-path'],
                    $this->configFilters['global']['java-path']
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

    /**
     * Returns a filters
     *
     * @param string|array $aliases Aliases
     *
     * @return array
     */
    private function getFiltersByAliases($aliases)
    {
        if (!is_array($aliases)) {
            $aliases = [$aliases];
        }
        $filters = [];
        foreach ($aliases as $alias) {
            $filters[] = $this->getFilter($alias);
        }

        return $filters;
    }

    /**
     * Checks filters configuration
     *
     * @param array $config Configuration
     *
     * @throws \LogicException
     * @return array
     */
    private function getFiltersConfig(array $config)
    {
        $defaults = [
            'global'             => [
                'yui-path'  => '/usr/share/yui-compressor',
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
        if (empty($config)) {
            return $defaults;
        }
        foreach ($defaults as $key => $item) {
            // Configuration for a filter is missing
            if (!array_key_exists($key, $config)) {
                $config[$key] = $item;
            } elseif (is_array($config[$key])) {
                foreach ($defaults[$key] as $name => $value) {
                    // Option for a filter configuration is missing
                    if (!array_key_exists($name, $config[$key])) {
                        $config[$key][$name] = $value;
                    }
                }
            } else {
                throw new \LogicException('Wrong filters configuration given');
            }
        }

        return $config;
    }

    /**
     * Returns valid assets config
     *
     * @param array $config Configuration
     *
     * @return array
     * @throws \LogicException
     */
    private function getAssetsConfig(array $config)
    {
        foreach ($config as $collectionName => &$values) {
            $values['type']            = explode('.', $collectionName);
            $values['type']            = '*.' . end($values['type']);
            $values['default_filters'] = isset($values['default_filters']) ? $values['default_filters'] : [];
            if (!array_key_exists('set', $values)) {
                throw new \LogicException(sprintf('Set is not exists in "%s" section', $collectionName));
            }
            if (!is_array($values['set'])) {
                throw new \LogicException(
                    sprintf(
                        'Set in "%s" section has wrong type. Expects array %s given',
                        $collectionName,
                        gettype($values['set'])
                    )
                );
            }
        }

        return $config;
    }

    /**
     * Check directories
     *
     * @param string $source
     * @param string $public
     * @param string $cache
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    private function checkDirectories($source, $public, $cache)
    {
        $directories = [
            'source' => $source,
            'cache'  => $cache,
            'public' => $public,
        ];
        foreach ($directories as $type => $dir) {
            if (!is_dir($dir)) {
                throw new \InvalidArgumentException(sprintf('%s directory "%s" does not exists', ucwords($type), $dir));
            }
            if (!is_writable($dir)) {
                throw new \InvalidArgumentException(sprintf('%s directory "%s" is not writable', ucwords($type), $dir));
            }
        }
        $directories = array_map(
            function ($dir) {
                return rtrim($dir, '\/') . DIRECTORY_SEPARATOR;
            },
            $directories
        );

        return $directories;
    }

}
