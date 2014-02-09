<?php

/**
 * Part of the Trillium
 *
 * @author  Kilte Leichnam <nwotnbm@gmail.com>
 * @package Trillium
 */

use Symfony\Component\HttpFoundation\Request;

$app = require __DIR__ . '/../application/application.php';
$app->run(Request::createFromGlobals());
