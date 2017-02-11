<?php

/**
 * Middleware for decoding JSON requests and providing the body as post paramaters.
 *
 * Copyright 2015 - PROJECT
 */

use Symfony\Component\HttpFoundation\Request;
use PROJECT\Exceptions;

$app->before(function (Request $request) use ($app) {
    if (in_array($request->getMethod(), ['POST', 'PUT', 'DELETE'])) {
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            // if (!$data) {
            //     throw new Exceptions\InvalidJsonException('errors.invalid_json');
            // }
            $request->request->replace(is_array($data) ? $data : []);
        }
    }
});
