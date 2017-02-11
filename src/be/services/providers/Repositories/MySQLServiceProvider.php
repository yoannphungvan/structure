<?php

namespace PROJECT\Services\Providers;

use Silex\Application;
use Silex\ServiceProviderInterface;
use PROJECT\Services\MySQL;

class MySQLServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['database'] = $app->share(function ($app) {
            return new MySQL(
                $app['database.options']['host'],
                $app['database.options']['username'],
                $app['database.options']['password'],
                $app['database.options']['db'],
                $app['database.options']['port']
            );
        });
    }

    public function boot(Application $app)
    {
    }
}
