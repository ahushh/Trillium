<?php

$finder = \Symfony\CS\Finder\DefaultFinder::create()
    ->ignoreDotFiles(true)
    ->ignoreVCS(true)
    ->notName('composer.*')
    ->exclude('vendor')
    ->exclude(glob('application/resources/*', GLOB_ONLYDIR))
    ->in(__DIR__)
;

return \Symfony\CS\Config\Config::create()->finder($finder);