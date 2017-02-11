<?php

  namespace PROJECT\Services\Providers;

  use Silex\Application;
  use Silex\ServiceProviderInterface;
  use PROJECT\Services\User;

  class UserServiceProvider implements ServiceProviderInterface
  {
    /**
     * @inheritdoc
     */
    public function register(Application $app)
    {
      $app['service.user'] = $app->share(function($app) {
        return new User();
      });
    }

    /**
     * @inheritdoc
     */
    public function boot(Application $app)
    {

    }
  }
