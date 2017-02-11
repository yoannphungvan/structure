<?php

namespace PROJECT\Controllers;

use Silex\Application;

interface RestControllerInterface
{
    public function getList(Application $app);

    // public function getOne(Application $app, $id);

    // public function post(Application $app);

    // public function put(Application $app, $id);

    // public function patch(Application $app, $id);

    // public function delete(Application $app, $id);
}