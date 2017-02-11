<?php

namespace PROJECT\Controllers;

use Silex\Application;

class DefaultController
{
    const TWIG_TPL_EXTENSION = '.html';

    public function renderPage(Application $app, $template)
    {
        return $app['twig']->render($template . self::TWIG_TPL_EXTENSION);
    } 
}