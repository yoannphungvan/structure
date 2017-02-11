<?php

/* ---------------------------------------------------------
 * configs/configs.sample.php
 *
 * Sample configurations.
 *
 * Copyright 2015 - PROJECT
 * ---------------------------------------------------------*/

// Debug mode disabled
$app['debug'] = true;

// Twig cache
$app['twig.options.cache'] = false;

// Redis cache
$app['predis.host'] = '4.4.4.17';
$app['predis.port'] = '6379';

// Local
$app['locale'] = 'en_US';
$app['domain'] = 'en';
$app['charset'] = 'UTF-8';

// MySQL Server
//$app['mysql.host'] = '10.209.164.1';
$app['mysql.host'] = 'localhost';
$app['mysql.username'] = 'wordr';
$app['mysql.password'] = '123456';
$app['mysql.db'] = 'wordr';
$app['mysql.port'] = '3306';

// Monolog
$app['monolog.name'] = 'app';
$app['monolog.level'] = Monolog\Logger::DEBUG;
$app['monolog.logfile'] = PATH_LOGS . '/' . $app['monolog.name'] . '.log';

$app['monolog.session_id'] = uniqid(rand());

if (!defined('CORS_ALLOW_ORIGIN')) {
    DEFINE('CORS_ALLOW_ORIGIN', '*');
}

// JWT security
$app['security.jwt'] = [
    'secret_key' => '1Gwk7s1AdQaj014TCq4yiVG5JlF2qc94',
    'life_time'  => 86400,
    'algorithm'  => ['HS256'],
    'login_route'  => 'api/user/login',
    'header_name' => 'X-Access-Token',
    'token_prefix' => 'Bearer',
];
