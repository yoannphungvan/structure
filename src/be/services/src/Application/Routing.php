<?php

namespace PROJECT\Services;

use MJanssen\Provider\RoutingServiceProvider;

class Routing extends RoutingServiceProvider
{
    public function addRoute(Application $app, array $route, $name = '')
    {
        $manager = !empty($route['manager']) ? $route['manager'] : null;

        $app = $this->addServices($app, $manager);
        parent::addRoute($app, $route, $name);
    }

    private function addServices(Application $app, $manager)
    {
        var_dump($manager);
        if (!empty($manager)) {
            $app['manager'] = $app['manager.' . $manager];
        }

        return $app;
    }
}