<?php

namespace PROJECT\Controllers;

use Silex\Application;

class SMSController
{
    public function sendSMS(Application $app)
    {
        $app['service.twilio']->sendSMS(
            $app->configs['messaging']['twilio']['phonenumber'],
            '+15147465522',
            'hello ca va ? http://www.google.com'
        );

        return true;
    }

    public function receiveSMS(Application $app)
    {
        error_log(json_encode($app['request']->request->all()));

        $app['service.twilio']->sendSMS(
            $app->configs['messaging']['twilio']['phonenumber'],
            '+15147465522',
            $app['request']->request->get('Body') . ' toi meme'
        );

        return true;
    }
}