<?php

namespace PROJECT\Controllers;

use Silex\Application;
use PROJECT\Services\Filters\Filters;
use PROJECT\Models\Managers;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Base controller.
 **/
abstract class BaseController
{

    /**
     * Get a list of objects.
     *
     * @param Application $app An Application instance
     * @return string A JSON
     **/
    public function getList(Application $app)
    {
        if (!isset($app['manager'])) {
            return;
        }

        $filters = $app['request']->query->all();
        $objects = $app['manager']->getList($filters);
        return $objects;
    }

    /**
     * Return a model matching the given ID
     *
     * @param integer $id
     * @param array $filters
     * @return mixed
     */
    public function getById(Application $app, $id)
    {
        if (!isset($app['manager'])) {
            return;
        }

        return $app['manager']->getById($id, $this->filters->toArray());
    }

    /**
     * Get an object.
     *
     * @param integer $id An id
     * @return string A JSON
     **/
    public function getObject(Application $app, $id)
    {
        if (!isset($app['manager'])) {
            return;
        }

        $object = $app['manager']->getById($id, $this->filters->toArray());
        $this->assertObject($this->app, $id, $object);

        return $object;
    }

    /**
     * Send a request to a controller.
     *
     * @param string $uri uri to send request to
     * @param string $method the request method
     * @param array $parameters parameters of the request
     * @return mixed the content of the response
     **/
    public function requestController(Application $app, $uri, $method, $parameters)
    {
        $request = Request::create($uri, $method, $parameters);
        $response = $this->app->handle($request, HttpKernelInterface::SUB_REQUEST, false);

        return $response->getContent();
    }

    /**
     * [create description]
     * @param  Application $app [description]
     * @return [type]           [description]
     */
    public function create(Application $app)
    {
        if (!isset($app['manager'])) {
            return;
        }


        $fields = $this->getFieldsFromRequest($app, $app['manager']);
        $object = $app['manager']->create($fields);
        $id = $app['manager']->persist($object);
        $object->id = $id;
        $this->maskValues($object);

        return $object;
    }

    /**
     * Update an object.
     *
     * @param Application $app An Application instance
     * @param integer $id An id
     * @return string A JSON
     **/
    public function update(Application $app, $id)
    {
        if (!isset($app['manager'])) {
            return;
        }

        $fields = $this->getFieldsFromRequest($app, $app['manager']);
        $object = $app['manager']->getById($id);

        $this->assertObject($app, $id, $object);
        $object = $app['manager']->prepareUpdate($object, $fields);
        $app['manager']->persist($object);
        $this->maskValues($object);

        return $object;
    }

    /**
     * Delete an object.
     *
     * @param Application $app An Application instance
     * @param integer $id An id
     * @return string A JSON
     **/
    public function delete(Application $app, $id)
    {
        if (!isset($app['manager'])) {
            return;
        }

        $object = $app['manager']->getById($id);
        $this->assertObject($app, $id, $object);

        $app['manager']->delete($id);

        $this->maskValues($object);

        return $object;
    }
}
