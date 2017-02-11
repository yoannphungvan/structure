<?php

  namespace PROJECT\Services\Providers;

  use Silex\Application;
  use Silex\ServiceProviderInterface;
  use PROJECT\Services\Firebase;

  class FirebaseServiceProvider implements ServiceProviderInterface
  {
    /**
     * @inheritdoc
     */
    public function register(Application $app)
    {
      $app['service.firebase'] = $app->share(function($app) {
        return new Firebase(
          isset($app['firebase.url']) ? $app['firebase.url'] : null,
          isset($app['firebase.token']) ? $app['firebase.token'] : null,
          isset($app['firebase.defaultpath']) ? $app['firebase.defaultpath'] : null        
        );
      });
    }

    /**
     * @inheritdoc
     */
    public function boot(Application $app)
    {

    }
  }
