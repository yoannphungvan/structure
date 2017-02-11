<?php

use Symfony\Bridge\Twig\Extension\RoutingExtension;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;
use Monolog\Handler\StreamHandler;
use JDesrosiers\Silex\Provider\CorsServiceProvider;

use PROJECT\Services\ModelManagerFactory;
use PROJECT\Services\Providers\MySQLServiceProvider;
use PROJECT\Services\Providers\RedisServiceProvider;
use PROJECT\Services\Providers\JwtAuthentificationServiceProvider;
use PROJECT\Services\Providers\TwilioServiceProvider;

// MySQL service provider
$app->register(new MySQLServiceProvider(), array(
    'database.options' => [
        'host'     => $app->configs['repositories']['mysql']['host'],
        'username' => $app->configs['repositories']['mysql']['username'],
        'password' => $app->configs['repositories']['mysql']['password'],
        'db'       => $app->configs['repositories']['mysql']['db'],
        'port'     => $app->configs['repositories']['mysql']['port']
    ]
));

// Redis cache service provider
$app->register(new RedisServiceProvider(), array(
    'cache.options' => array(
        'host'   => $app->configs['repositories']['redis']['host'],
        'port'   => $app->configs['repositories']['redis']['port'],
        'scheme' => 'tcp'
    )
));

$app->register(new CorsServiceProvider(), array(
    "cors.allowOrigin" => $app->configs['cors']['domains'],
));

// Twig template engine
$app['twig'] = $app->share(
    function ($app) {
        $options = array(
            'cache' => isset($app->configs['twig.options.cache']) ? $app->configs['twig.options.cache'] : false,
            'charset' => $app->configs['localisation']['charset'],
            'debug' => $app->configs['debug'],
            'strict_variables' => $app->configs['debug']
        );

        $twig = new \Twig_Environment($app['twig.loader'], $options);
        $twig->addGlobal('app', $app);

        if ($app->configs['debug']) {
            $twig->addExtension(new \Twig_Extension_Debug());
        }

        $twig->addExtension(new RoutingExtension($app['url_generator']));

        return $twig;
    }
);

// Url generator
$app['url_generator'] = $app->share(function ($app) {
    $app->flush();
    return new UrlGenerator($app['routes'], $app['request_context']);
});

$app['twig.loader'] = $app->share(function ($app) {
    return new \Twig_Loader_Filesystem(array($app->getPath('views')));
});

// Model manager factory
$app['factories.model_manager'] = $app->share(function () use ($app) {
    return new ModelManagerFactory($app['database'], $app['cache']);
});

// Translator
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale_fallbacks' => array('en')
));

$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => $app->configs['logging']['monolog']['logfile'],
    'monolog.level' => $app->configs['logging']['monolog']['level'],
    'monolog.name' => $app->configs['logging']['monolog']['name']
));

$app['monolog'] = $app->share($app->extend('monolog', function($monolog, $app) {
    $handler = new StreamHandler($app->configs['logging']['monolog']['logfile'], $app->configs['logging']['monolog']['level']);
    $app->configs['monolog.handler'] = $handler;
    $monolog->pushProcessor(function ($record) use ($app) {
        $record['extra']['logsessionid'] = $app->configs['logging']['monolog']['session_id'];
        return $record;
    });
    return $monolog;
}));

$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'   => 'pdo_mysql', // or pdo_mysql, pdo_pgsql, pdo_*
        'path'     => PATH_ROOT.'/app.db',
    ),
));

// Register this provider so we can write controllers as services.
$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->register(new JwtAuthentificationServiceProvider());
$app->register(new \Silex\Provider\ValidatorServiceProvider());
$app->register(new TwilioServiceProvider());


// $app['security.jwt'] = [
//     'secret_key' => 'vfowvfj23jf4dfr',
//     'life_time'  => 86400,
//     'options'    => [
//         'username_claim' => 'sub', // default name, option specifying claim containing username
//         'header_name' => 'X-Access-Token', // default null, option for usage normal oauth2 header
//         'token_prefix' => 'Bearer',
//     ]
// ];

// $app['users'] = function () use ($app) {
//     $users = [
//         'admin' => array(
//             'roles' => array('ROLE_ADMIN'),
//             // raw password is foo
//             'password' => '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg==',
//             'enabled' => true
//         ),
//     ];

//     return new InMemoryUserProvider($users);
// };

// $app['security.firewalls'] = array(
//     'login' => [
//         'pattern' => 'login|register|oauth',
//         'anonymous' => true,
//     ],
//     'secured' => array(
//         'pattern' => '^.*$',
//         'logout' => array('logout_path' => '/logout'),
//         'users' => $app['users'],
//         'jwt' => array(
//             'use_forward' => true,
//             'require_previous_session' => false,
//             'stateless' => true,
//         )
//     ),
// );

// $app->register(new Silex\Provider\SecurityServiceProvider());
// $app->register(new Silex\Provider\SecurityJWTServiceProvider());

// $app->register(new ErrorHandler(), array(

// ));
