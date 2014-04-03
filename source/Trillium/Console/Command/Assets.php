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
     * @var array File types
     */
    private $types = [
        'js',
        'css'
    ];

    /**
     * @var array Loaded filters
     */
    private $filters = [];

    /**
     * @var array Assets configuration
     */
    private $conf;

    /**
     * @var array Filters configuration (from file)
     */
    private $filtersConfUser;

    /**
     * @var array Default filters configuration
     */
    private $filtersConfDefault = [
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
        'dir_invalid'        => '<fg=red>[EE]</fg=red> %s directory does not exists',
        'dir_ok'             => '<info>[OK]</info> %s directory: %s',
        'ignore'             => '<info>Ignore %s</info>',
        'remove_cache'       => 'Remove cache',
        'build'              => "\nWill now build...",
        'assets_type'        => "\nType: %s",
        'not_found'          => '<fg=red>[WW]</fg=red> No assets found.',
        'found'              => '%s assets found',
        'load_from_cache'    => "from cache",
        'load_from_source'   => "from source",
        'overwrite_asset'    => "\t<fg=red>[WW]</fg=red> Overwrite \"%s\" by \"%s\"",
        'found_asset'        => "\tFound: %s \"%s\" with \"%s\" priority [%s]",
        'dump_assets'        => 'Dump "%s" into "%s"... ',
        'dump_success'       => '<info>[OK]</info>',
        'dump_failed'        => '<fg=red>[FAIL]</fg=red>',
        'success'            => '<info>Success</info>',
        'failed'             => '<fg=red>Nothing to build</fg=red>',
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
        $this->directories['source'] = $source;
        $this->directories['public'] = $public;
        $this->directories['cache']  = $cache;
        // Load filters configuration
        $this->conf = $conf;
        if (isset($this->conf['filters'])) {
            $this->filtersConfUser = $this->conf['filters'];
            foreach ($this->filtersConfDefault as $key => $item) {
                // Configuration for a filter is missing
                if (!array_key_exists($key, $this->filtersConfUser)) {
                    $this->filtersConfUser[$key] = $item;
                } elseif (is_array($this->filtersConfUser[$key])) {
                    foreach ($this->filtersConfDefault[$key] as $name => $value) {
                        // Option for a filter configuration is missing
                        if (!array_key_exists($name, $this->filtersConfUser[$key])) {
                            $this->filtersConfUser[$key][$name] = $value;
                        }
                    }
                } else {
                    throw new \LogicException('Unable to read the configuration file');
                }
            }
            unset($this->conf['filters']);
        } else {
            $this->filtersConfUser = $this->filtersConfDefault;
        }
        parent::__construct('assets');
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
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
                'js',
                'j',
                InputOption::VALUE_OPTIONAL,
                'Javascript result filename',
                'scripts.js'
            )
            ->addOption(
                'css',
                'c',
                InputOption::VALUE_OPTIONAL,
                'Styles result filename',
                'styles.css'
            )->addOption(
                'disable-cache',
                'd',
                InputOption::VALUE_NONE,
                'Ignore and clear cache'
            );
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $cacheEnabled = !$input->getOption('disable-cache');
        $errors       = 0;
        $filesystem   = new Filesystem();
        $ignore       = $input->getOption('ignore');
        $names        = [];
        $verbosity    = $output->getVerbosity() === OutputInterface::VERBOSITY_VERBOSE;
        foreach ($this->types as $type) {
            $names[$type] = $input->getOption($type);
        }
        if ($ignore !== null && !in_array($ignore, $this->types)) {
            $errors++;
            $output->writeln($this->messages['wrong_ignore_value']);
        }
        // Check directories
        foreach ($this->directories as $dirType => $dirPath) {
            $dirPath = realpath($dirPath);
            if ($dirPath === false) {
                $errors++;
                $output->writeln(sprintf($this->messages['dir_invalid'], ucwords($dirType)));
            } else {
                $this->directories[$dirType] = rtrim($dirPath, '\/') . DIRECTORY_SEPARATOR;
                $output->writeln(sprintf($this->messages['dir_ok'], ucwords($dirType), $this->directories[$dirType]));
            }
        }
        if ($errors > 0) {
            return 1;
        }
        // Ignore type
        if ($ignore !== null) {
            unset($this->types[array_search($ignore, $this->types)]);
            $output->writeln(sprintf($this->messages['ignore'], $ignore));
        }
        // Load checksums
        $checksums    = [];
        $checksumsNew = [];
        if (is_file($this->directories['cache'] . self::CHECKSUMS)) {
            $checksumsRaw = @file_get_contents($this->directories['cache'] . self::CHECKSUMS);
            if ($checksumsRaw !== false) {
                $checksums = json_decode($checksumsRaw, true);
            }
            unset($checksumsRaw);
        }
        // Remove cache
        if (!$cacheEnabled) {
            $output->writeln($this->messages['remove_cache']);
            $filesystem->remove(
                array_map(
                    function ($name) {
                        return $this->directories['cache'] . $name;
                    },
                    array_diff(scandir($this->directories['cache']), ['.', '..', '.gitignore'])
                )
            );
        }
        /**
         * @var $sorted FileAsset[]
         * @var $file   \Symfony\Component\Finder\SplFileInfo
         */
        $output->writeln($this->messages['build']);
        $i = 0;
        foreach ($this->types as $type) {
            $output->writeln(sprintf($this->messages['assets_type'], $type));
            $conf = $this->conf[$type];
            if (!isset($conf['filters'])) {
                $conf['filters'] = [];
            }
            if (!isset($conf['priority'])) {
                $conf['priority'] = [];
            }
            $defaultFilters = isset($conf['filters']['*']) ? $conf['filters']['*'] : [];
            $collection     = [];
            $sorted         = [];
            $iterator       = $this->getIterator('*.' . $type, $this->directories['source']);
            $total          = iterator_count($iterator);
            if ($total === 0) {
                $output->writeln($this->messages['not_found']);
                continue;
            }
            $output->writeln(sprintf($this->messages['found'], $total));
            $a = 1;
            foreach ($iterator as $file) {
                $baseName       = $file->getBasename();
                $realPath       = $file->getRealPath();
                $cachedName     = strtr($file->getRelativePath(), ['\\' => '_', '/' => '_']) . $baseName;
                $hash           = md5_file($realPath);
                $key            = str_replace($this->directories['source'], '', $realPath);
                $priority       = isset($conf['priority'][$key]) ? (int) $conf['priority'][$key] : null;
                $filtersAliases = isset($conf['filters'][$key]) ? $conf['filters'][$key] : $defaultFilters;
                $filters        = [];
                $cached         = false;
                $cacheExpired   = !array_key_exists($realPath, $checksums) || $checksums[$realPath] != $hash;
                if (is_file($this->directories['cache'] . $cachedName) && $cacheEnabled && !$cacheExpired) {
                    $path   = $this->directories['cache'] . $cachedName;
                    $cached = true;
                } else {
                    $path = $realPath;
                    if (is_array($filtersAliases)) {
                        foreach ($filtersAliases as $filter) {
                            $filters[] = $this->getFilterByAlias($filter);
                        }
                    } elseif (!empty($filtersAliases)) {
                        $filters[] = $this->getFilterByAlias($filtersAliases);
                    }
                }
                if ($verbosity) {
                    $output->writeln(
                        sprintf(
                            $this->messages['found_asset'],
                            $a . '/' . $total,
                            $key,
                            $priority !== null ? $priority : 'unspecified',
                            $cached ? $this->messages['load_from_cache'] : $this->messages['load_from_source']
                        )
                    );
                }
                $asset = new FileAsset($path, $filters);
                if (!$cached) {
                    // Write asset to cache
                    $filesystem->dumpFile($this->directories['cache'] . $cachedName, $asset->dump());
                }
                if ($priority !== null) {
                    if (isset($sorted[$priority])) {
                        $sourceKey = str_replace($this->directories['source'], '', $sorted[$priority]->getSourceRoot());
                        $sourceKey .= '/' . $sorted[$priority]->getSourcePath();
                        $output->writeln(sprintf($this->messages['overwrite_asset'], $sourceKey, $key));
                    }
                    $sorted[$priority] = $asset;
                } else {
                    $collection[] = $asset;
                }
                $checksumsNew[$realPath] = $hash;
                $a++;
            }
            ksort($sorted);
            $collection     = array_merge($sorted, $collection);
            $collection     = new AssetCollection($collection);
            $collectionPath = $this->directories['public'] . $names[$type];
            $output->write(sprintf($this->messages['dump_assets'], $type, $collectionPath));
            $filesystem->dumpFile($collectionPath, $collection->dump());
            $filesystem->dumpFile($this->directories['cache'] . self::CHECKSUMS, json_encode($checksumsNew));
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
     * @throws \LogicException Filter does not exists
     * @return FilterInterface
     */
    private function getFilterByAlias($alias)
    {
        if (isset($this->filters[$alias])) {
            return $this->filters[$alias];
        }
        $conf = $this->filtersConfUser[$alias];
        switch ($alias) {
            case 'yui-js-compressor':
                $compressor = new JsCompressorFilter(
                    $this->filtersConfUser['global']['yui-path'],
                    $this->filtersConfUser['global']['java-path']
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
                    $this->filtersConfUser['global']['yui-path'],
                    $this->filtersConfUser['global']['java-path']
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
