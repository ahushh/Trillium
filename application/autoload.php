<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

use Composer\Autoload\ClassLoader;
use Symfony\Component\ClassLoader\ApcClassLoader;

require __DIR__ . '/../vendor/composer/ClassLoader.php';
require __DIR__ . '/../vendor/symfony/class-loader/Symfony/Component/ClassLoader/ApcClassLoader.php';

$loader = new ClassLoader();
$map = require __DIR__ . '/../vendor/composer/autoload_namespaces.php';
foreach ($map as $namespace => $path) {
    $loader->set($namespace, $path);
}
$map = require __DIR__ . '/../vendor/composer/autoload_psr4.php';
foreach ($map as $namespace => $path) {
    $loader->setPsr4($namespace, $path);
}
$classMap = require __DIR__ . '/../vendor/composer/autoload_classmap.php';
if ($classMap) {
    $loader->addClassMap($classMap);
}
if (php_sapi_name() !== 'cli') {
    $loader = new ApcClassLoader('trillium', $loader);
}

$loader->register();
