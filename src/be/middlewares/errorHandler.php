<?php

/**
 * Error handling
 */
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PROJECT\Exceptions;

$app->error(function (\Exception $e, $code) use ($app) {

    if (strpos($app['request']->getRequestUri(), '/api') !== false) {
        $app['logger']->addError('API ERROR :: ' . $e->getMessage() . ' :: file :: ' . $e->getFile(). ' :: get Line :: ' . $e->getLine(), $e->getTrace());
        $response = $app->json([
            'status' => 'error' ,
            'code'   => $e->getCode(),
            'response' => $e->getMessage()
        ]);
    } else {
        $app['logger']->addError('Webpage ERROR :: ' . $e->getMessage() . ' :: file :: ' . $e->getFile(). ' :: get Line :: ' . $e->getLine(), $e->getTrace());
        //To do: display customer error page
        $response = $e->getMessage();
    }

    return $response;
});
