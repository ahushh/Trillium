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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Trillium\General\Application;

/**
 * Assets Class
 *
 * @package Trillium\Command
 */
class Assets extends Command
{

    /**
     * @var Application An application instance
     */
    private $app;

    /**
     * {@inheritdoc}
     * @param Application $app An application instance
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        parent::__construct('assets');
        $this->setDescription('Build assets via assetic');
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->addArgument(
                'ignore',
                InputArgument::OPTIONAL,
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
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $ignore = $input->getArgument('ignore');
        $cssName = $input->getOption('stylesheet');
        $jsName = $input->getOption('javascript');
        if ($ignore !== null) {
            if (!in_array($ignore, ['css', 'js'])) {
                $output->writeln('<error>Wrong ignore value given</error>');

                return 1;
            }
            $output->writeln('<info>Ignore ' . $ignore . '</info>');
        }
        if (empty($jsName)) {
            $output->writeln('<error>Javascript filename can not be empty</error>');
        }
        if (empty($cssName)) {
            $output->writeln('<error>Stylesheet filename can not be empty</error>');
        }
        $sourceDirectory = realpath($this->app->getSourceAssetsDir()) . '/';
        $publicDirectory = realpath($this->app->getPublicAssetsDir()) . '/';
        if ($sourceDirectory === false) {
            $output->writeln('<error>[FAIL]</error> Source directory does not exists');

            return 1;
        }
        if ($publicDirectory === false) {
            $output->writeln('<error>[FAIL]</error> Public directory does not exists');

            return 1;
        }
        $output->writeln([
            '<info>[OK]</info> Source directory: ' . $sourceDirectory,
            '<info>[OK]</info> Public directory: ' . $publicDirectory,
            '<info>Will now build...</info>'
        ]);
        if ($this->build($sourceDirectory, $publicDirectory, $ignore, $cssName, $jsName) === true) {
            $output->writeln('<info>Success</info>');
        } else {
            $output->writeln('<error>Nothing to dump</error>');
        }

        return 0;
    }

    /**
     * Build assets
     *
     * Returns false, if source directory is empty
     *
     * @param string $sourceDirectory Path to the source directory
     * @param string $publicDirectory Path to the public directory
     * @param string $ignore          Ignored files (css, js)
     * @param string $cssName         Css result filename
     * @param string $jsName          Js result filename
     *
     * @return boolean
     */
    private function build($sourceDirectory, $publicDirectory, $ignore, $cssName, $jsName)
    {
        if ($ignore !== 'css') {
            $cssIterator = $this->getIterator('*.css', $sourceDirectory);
        }
        if ($ignore !== 'js') {
            $jsIterator = $this->getIterator('*.js', $sourceDirectory);
        }
        if (!isset($cssIterator, $jsIterator)) {
            return false;
        }
        // TODO: sort and define filters for each asset using configuration
        /**
         * @var $file \Symfony\Component\Finder\SplFileInfo
         */
        $css = [];
        $js = [];
        foreach ($cssIterator as $file) {
            $css[] = new FileAsset($file->getRealPath());
        }
        foreach ($jsIterator as $file) {
            $js[] = new FileAsset($file->getRealPath());
        }
        $cssSize = sizeof($css);
        $jsSize = sizeof($js);
        if ($cssSize === 0 && $jsSize === 0) {
            return false;
        }
        $fs = new Filesystem();
        if ($cssSize > 0) {
            $css = new AssetCollection($css);
            $fs->dumpFile($publicDirectory . $cssName, $css->dump());
        }
        if ($jsSize > 0) {
            $js = new AssetCollection($js);
            $fs->dumpFile($publicDirectory . $jsName, $js->dump());
        }

        return true;
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

}
