<?php

/* ---------------------------------------------------------
 * web/app.php
 *
 * Copyright 2016 - PROJECT
 * ---------------------------------------------------------*/

use PROJECT\Services\Application;
use PROJECT\Services\Configs;
use Silex\ControllerProviderInterface;

define('PATH_ROOT', dirname(dirname(__DIR__)));

// Autoload
require_once PATH_ROOT . '/vendor/be/autoload.php';

$app = new Application();

// Paths
$app->setRootPath(PATH_ROOT);
$app->setPath('logs', 'logs');
$app->setPath('configs', 'configs');
$app->setPath('routes', 'configs/routes');
$app->setPath('locales', 'locales');
$app->setPath('src', 'src/be');
$app->setPath('services', 'src/be/services');
$app->setPath('middlewares', 'src/be/middlewares');
$app->setPath('vendor', 'vendor/be');
$app->setPath('web', 'web');
$app->setPath('views', 'src/views');

// Configs
$configs = new Configs($app->getEnv());
$configs->loadFile($app->getPath('configs') . '/configs-shared.php');
$configs->loadFile($app->getPath('configs') . '/'. $app->getEnv() . '/configs.php');
$app->setConfigs($configs->getConfigs());

// Locales
$app->setLocale(
	$app->configs['localisation']['domain'], 
	$app->getPath('locales'), 
	$app->configs['localisation']['locale'], 
	$app->configs['localisation']['charset']
);

// Middlewares
$app->setMiddleware($app->getPath('middlewares') . '/authentification.php'); 
$app->setMiddleware($app->getPath('middlewares') . '/errorHandler.php');
$app->setMiddleware($app->getPath('middlewares') . '/jsonDecoder.php');
$app->setMiddleware($app->getPath('middlewares') . '/response.php');

// Services
$app->setServices($app->getPath('services') . '/common.php');
$app->setServices($app->getPath('services') . '/managers.php');

// Routes
$app->addRoutes($app->getPath('routes') . '/default.php');
exit;
return $app;
