<?php

namespace PROJECT\Services;

use Silex\Application as SilexApplication;

class Application extends SilexApplication
{
    protected $app;
    protected $env;
    protected $paths;
    public    $configs;
    protected $routes;
    protected $controllers;
    protected $managers;
    protected $services;
    private static $rootPath;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->app = parent::__construct();
        $this->routing = new Routing();
        $this->env = $this->getEnv();
        $this->configs = [];
    }

    public function setRootPath($rootPath)
    {
        self::$rootPath = $rootPath;
    }

    public function getRootPath()
    {
        return self::$rootPath;
    }

    public function setPath($key, $path)
    {
        $this->paths[$key] = $this->getRootPath() . '/' . $path;
    }

    public function getPaths()
    {
        return $this->paths;
    }

    public function getPath($key)
    {
        return $this->paths[$key];
    }

    public function getEnv()
    {
        $env = 'dev';
        if (getenv('ENV')) {
          $env = getenv('ENV');
        }

        return $env;
    }

    public function setConfigs($configs)
    {
        $this->configs = $configs;
    }

    public function getConfigs()
    {
        return $this->configs;
    }

    public function setMiddleware($middlewareFile)
    {
        $app = $this;
        require $middlewareFile;
    }

    public function setServices($servicesFile)
    {
        $app = $this;
        require $servicesFile;
    }

    public function setLocale($domain, $pathLocales, $locale, $charset)
    {
        // Set language
        putenv('LC_ALL=' . $locale . '.' . $charset);
        setlocale(LC_ALL, $locale . '.' . $charset);

        // Specify the location of the translation tables
        bindtextdomain($domain, $pathLocales);
        bind_textdomain_codeset($domain, $charset);

        // Choose domain
        textdomain($domain);
    }

    public function addRoutes($routesPath) 
    {
        $routes = require $routesPath;
        $this->routing->addRoutes($this, $routes);
    }
}