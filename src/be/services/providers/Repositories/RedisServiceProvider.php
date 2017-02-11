<?php

namespace PROJECT\Services\Providers;

use Silex\Application;
use Silex\ServiceProviderInterface;
use PROJECT\Models\Entities\Predis;

class RedisServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['cache'] = $app->share(function ($app) {
            return Predis::getInstance(
                $app['cache.options']['host'],
                $app['cache.options']['port'],
                $app['cache.options']['scheme']
            );
        });
    }

    public function boot(Application $app)
    {
    }
}
