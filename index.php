<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Support both layouts:
// 1) document root at project root
// 2) document root at public-like folder where project is one level up
$basePath = __DIR__;
if (! file_exists($basePath.'/vendor/autoload.php') && file_exists($basePath.'/../vendor/autoload.php')) {
    $basePath = dirname(__DIR__);
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = $basePath.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require $basePath.'/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once $basePath.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
