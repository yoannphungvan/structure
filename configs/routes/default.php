<?php

return [
    [
        'name'       => 'home',
        'pattern'    => '/',
        'controller' => 'PROJECT\Controllers\DefaultController::renderPage',
        'method'     => ['get'],
        'value'      => ['template' => 'home']
        //'before' => array(new MiddleWare())
    ],
    [
        'pattern'    => '/api/country',
        'controller' => 'PROJECT\Controllers\CountryController::getList',
        'method'     => ['get']
    ],
    [
        'pattern'    => '/api/sms/send',
        'controller' => 'PROJECT\Controllers\SMSController::sendSMS',
        'method'     => ['get']
    ],
    [
        'pattern'    => '/api/sms/receive',
        'controller' => 'PROJECT\Controllers\SMSController::receiveSMS',
        'method'     => ['post']
    ],
    [
        'pattern'    => '/api/user',
        'controller' => 'PROJECT\Controllers\UserController::getList',
        'method'     => ['get'],
        'manager'    => 'user'
    ],
];

//*******************************************************************************************
//***************************************   Pages   *****************************************
//*******************************************************************************************
$app->get('/', 'controller.default:renderPage')->value('template', 'home');

//*******************************************************************************************
//***************************************   API   *******************************************
//*******************************************************************************************

// Country
$app->get('/api/country', 'controller.country:getList');
$app->get('/api/country/{id}', 'controller.country:getOne');

// Users
$app->get('/api/user', 'controller.user:getList');
$app->get('/api/user/{id}', 'controller.user:getOne');
$app->post('/api/user', 'controller.user:post');
$app->put('/api/user/{id}', 'controller.user:put');
$app->delete('/api/user/{id}', 'controller.user:delete');
$app->post('/api/user/login', 'controller.user:login');
$app->post('/api/user/resetpassword', 'controller.user:resetpassword');
$app->post('/api/user/sendresetpasswordemail', 'controller.user:sendResetpasswordEmail');

// User word
$app->get('/api/user/{userId}/word', 'controller.user:getWordList');
$app->get('/api/user/{userId}/word/{wordId}', 'controller.user:getWordById');
$app->post('/api/user/{userId}/word', 'controller.user:saveWord');
$app->put('/api/user/{id}/word/{wordId}', 'controller.user:updateWord');
$app->delete('/api/user/{id}/word/{wordId}', 'controller.user:deleteWord');
$app->get('/api/user/{userId}/timer', 'controller.user:getTimer');

// Word
$app->get('/api/word', 'controller.word:getList');
$app->get('/api/word/{id}', 'controller.word:getOne');


// Healthcheck
$app->get('/api/healthcheck', 'controller.healthCheck:ping');

$app->after($app["cors"]);
