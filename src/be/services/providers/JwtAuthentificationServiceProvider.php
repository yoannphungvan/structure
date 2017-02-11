<?php
/**
 * Created by PhpStorm.
 * User: vanyaonn
 * Date: 15-12-14
 * Time: 3:49 PM
 */

namespace PROJECT\Services\Providers;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Silex\Component\Security\Core\Encoder\JWTEncoder;

class JwtAuthentificationServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app->configs['security.jwt'] = array_replace_recursive([
            'secret_key' => 'default_secret_key',
            'life_time' => 86400,
            'algorithm'  => ['HS256'],
            'options' => [
                'username_claim' => 'name',
                'header_name' => 'SECURITY_TOKEN_HEADER',
                'token_prefix' => null,
            ]
        ], $app->configs['security.jwt']);

        $app['security.jwt.encoder'] = function() use ($app) {
            return new JWTEncoder($app->configs['security.jwt']['secret_key'], $app->configs['security.jwt']['life_time'], $app->configs['security.jwt']['algorithm']);
        };
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     */
    public function boot(Application $app)
    {
    }
}
