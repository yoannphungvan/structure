<?php

namespace PROJECT\Controllers;

use Silex\Application;

class HealthCheckController extends RestController
{
    /**
     * Used by loadbalancer to healthcheck
     * Return 200 status if ok
     * Return 503 if not
     * @param Application $app An Application instance
     * @return json response
     **/
    public function ping(Application $app)
    {
        $response = [
            'status' => 'ok',
            'code'   => 200,
            'response' => 'PHP'
        ];

        return $app->json($response);
    }
}
