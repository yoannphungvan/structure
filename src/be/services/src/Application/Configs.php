<?php

namespace PROJECT\Services;

class Configs
{
    private $env;
    private $configs;

    /**
     * Constructor
     *
     * @param string $env
     */
    public function __construct($env)
    {
        $this->env = $env;
        $this->configs = [];
    }

    public function loadFile($fileName)
    {
        $configsToLoad = require_once $fileName;
        $this->configs = array_merge($this->configs, $configsToLoad);
    }

    public function getConfigs()
    {
        return $this->configs;
    }
}

