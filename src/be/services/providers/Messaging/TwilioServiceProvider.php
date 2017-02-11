<?php

  namespace PROJECT\Services\Providers;

  use Silex\Application;
  use Silex\ServiceProviderInterface;
  use PROJECT\Services\Twilio;

  class TwilioServiceProvider implements ServiceProviderInterface
  {
    /**
     * @inheritdoc
     */
    public function register(Application $app)
    {
      $app['service.twilio'] = $app->share(function($app) {
        return new Twilio(
          $app->configs['messaging']['twilio']['accountSid'],
          $app->configs['messaging']['twilio']['authToken']
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
