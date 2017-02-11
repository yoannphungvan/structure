<?php

namespace PROJECT\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Restful controller implementing standard get,post,update,delete HTTP functionality
 */
class RestController extends BaseController implements RestControllerInterface
{
    /**
     * Get a list of all models, json encoded. 
     *
     * @param Application $app An instance of the app
     * @return string|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getList(Application $app)
    {
        return $this->responseFormat(parent::getList($app));
    }

    /**
     * Returns a single model by ID
     *
     * @param Application $app An of the current app.
     * @param string $id The id of the model to fetch.
     *
     * @return string|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getOne(Application $app, $id)
    {
        $model = parent::getById($app, $id);
        return $this->responseFormat((array) $model);
    }

    /**
     * Route handler for creating new models.
     *
     * @param Application $app An instance of the app
     *
     * @return string|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function post(Application $app)
    {
        $object = parent::create($app);
        return $this->responseFormat((array) $object);
    }

    /**
     * Route handler for updating a model record in the DB.
     *
     * @param Application $app An instance of app.
     * @param Request $request An instance of incoming request
     *
     * @return string|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function put(Application $app, $id)
    {
        $object = parent::update($app, $id);
        return $this->responseFormat($object);
    }

    /**
     * Route handler for pathing a model record in the DB.
     *
     * @param Application $app An instance of app.
     * @param Request $request An instance of incoming request
     *
     * @return string|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function patch(Application $app, $id)
    {
        $object = parent::patch($app, $id);
        return $this->responseFormat($object);
    }

    /**
     * Delete an object based on the ID
     *
     * @param Application $app
     * @param int $id
     *
     * @return string|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function delete(Application $app, $id)
    {
        $object = parent::delete($app, $id);
        return $this->responseFormat($object);
    }

    public function responseFormat($data)
    {
        if ($data instanceOf \Exception) {
            $response = [
                'status' => 'error' ,
                'code'   => $data->getCode(),
                'response' => $data->getMessage()
            ];
        } else {
            $response = [
                'status' => 'ok' ,
                'code'   => '200',
                'response' => $data
            ];
        }
        return $this->app->json($response);
    }
}
