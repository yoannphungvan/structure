<?php

/* ---------------------------------------------------------
 * src/be/controllers/CountryController.php
 *
 * Geolocalisation Controller.
 *
 * Copyright 2015 - PROJECT
 * ---------------------------------------------------------*/

namespace PROJECT\Controllers

{
    use Silex\Application;
    use PROJECT\Models\Entities\Geolocalisation;
    use PROJECT\Models\Managers\ProductManager;
    use Symfony\Component\HttpFoundation\Request;

    /**
     * @api {get} /country Get list of countries
     * @apiName GetCountryList
     * @apiGroup Country
     * @apiHeader {String} X-Access-Token User unique access-token:  "Bearer{YOUR_TOKEN}".
     *
     * @apiSuccess {String} status ok
     * @apiSuccess {Number} code Code of the response
     * @apiSuccess {Object[]} response List of countries
     * @apiSuccess {Number} response.id Id of the country
     * @apiSuccess {String} response.code Code of the country
     * @apiSuccess {String} response.name Name of the country
     *
     * @apiError {String} status error
     * @apiError {Number} code Code of the error
     * @apiError {String} response Error messsage
     */

    /**
     * @api {get} /country/:id Get country information
     * @apiName GetCountry
     * @apiGroup Country
     * @apiHeader {String} X-Access-Token User unique access-token:  "Bearer{YOUR_TOKEN}".
     *
     * @apiParam {Number} id Country unique ID.
     *
     * @apiSuccess {String} status status of the response
     * @apiSuccess {Number} code Code of the response
     * @apiSuccess {Object} response A country
     * @apiSuccess {Number} response.id Id of the country
     * @apiSuccess {String} response.code Code of the country
     * @apiSuccess {String} response.name Name of the country
     *
     * @apiError {String} status error
     * @apiError {Number} code Code of the error
     * @apiError {String} response Error messsage
     */

    /**
     * Handles the addresses.
     **/
    class CountryController extends RestController
    {
        /**
         * Constructor.
         **/
        public function __construct(Application $app, $filters = null)
        {
            $this->manager = 'CountryManager';
            $this->objectCacheTag = 'country';
            $this->useCache = false;
            parent::__construct($app, $filters);
        }

        /**
         * Get country by Code
         * @param  Application $app An Application instance
         * @param  string $code A country code
         * @return string A JSON
         */
        public function getByCode(Application $app, $code)
        {
            $object = $this->modelManager->getByCode($code);
            return $app->json($object);
        }
    }
}
