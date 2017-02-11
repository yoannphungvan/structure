<?php

namespace PROJECT\Controllers;


use Silex\Application;
use PROJECT\Exceptions\BadRequestException;

/**
 * Handles the users.
 **/
class UserController extends RestController
{
    /**
     * @param Application $app
     * @return string|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function login(Application $app)
    {
        $email = $app['request']->request->get('email');
        $password = $app['request']->request->get('password');
        $objects = $this->modelManager->login($email, $password);

        return $this->responseFormat($objects);
    }

    /**
     * @param Application $app
     * @return string|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function resetpassword(Application $app)
    {
        $passwordToken = $app['request']->request->get('passwordToken');
        $newPassword   = $app['request']->request->get('newPassword');
        $objects = $this->modelManager->resetpassword($passwordToken, $newPassword);

        return $this->responseFormat($objects);
    }

    /**
     * @param Application $app
     * @return string|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function sendResetpasswordEmail(Application $app)
    {
        $email = $app['request']->request->get('email');
        $objects = $this->modelManager->sendResetpasswordEmail($email);

        return $this->responseFormat($objects);
    }

    /**
     * @param Application $app
     * @param $userId
     * @return string|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function delete(Application $app, $userId)
    {
        $object = $this->modelManager->delete($userId);
        return $this->responseFormat($object);
    }

    /**
     * @api {get} /user Get list of users
     * @apiName GetUserList
     * @apiGroup User
     * @apiHeader {String} X-Access-Token User unique access-token:  "Bearer{YOUR_TOKEN}".
     *
     * @apiSuccess {String} status ok
     * @apiSuccess {Number} code Code of the response
     * @apiSuccess {Object[]} response List of users
     * @apiSuccess {Number} response.id Id of the user
     * @apiSuccess {String} response.username Username of the user
     * @apiSuccess {String} response.firstname Firstname of the user
     * @apiSuccess {String} response.lastname Lastname of the user
     * @apiSuccess {String} response.email Email of the user
     * @apiSuccess {String} response.password Password of the user
     * @apiSuccess {String} response.description Description of the user
     * @apiSuccess {String} response.city City of the user
     * @apiSuccess {String} response.country_id Country id of the user
     * @apiSuccess {String} response.picture Picture of the user
     * @apiSuccess {String} response.active Active or not
     * @apiSuccess {String} response.last_login_date Last login date of the user
     * @apiSuccess {String} response.created_date Creation date of the user
     * @apiSuccess {String} response.modification_date Modification date of the user
     * @apiSuccess {String} response.deleted_date Deleted date of the user
     *
     * @apiError {String} status error
     * @apiError {Number} code Code of the error
     * @apiError {String} response Error messsage
     */

    /**
     * @api {get} /user/:id Get user information
     * @apiName GetUser
     * @apiGroup User
     * @apiHeader {String} X-Access-Token User unique access-token:  "Bearer{YOUR_TOKEN}".
     *
     * @apiParam {Number} id User unique ID.
     *
     * @apiSuccess {String} status status of the response
     * @apiSuccess {Number} code Code of the response
     * @apiSuccess {Object} response A User
     * @apiSuccess {Number} response.id Id of the user
     * @apiSuccess {String} response.username Username of the user
     * @apiSuccess {String} response.firstname Firstname of the user
     * @apiSuccess {String} response.lastname Lastname of the user
     * @apiSuccess {String} response.email Email of the user
     * @apiSuccess {String} response.password Password of the user
     * @apiSuccess {String} response.description Description of the user
     * @apiSuccess {String} response.city City of the user
     * @apiSuccess {String} response.country_id Country id of the user
     * @apiSuccess {String} response.picture Picture of the user
     * @apiSuccess {String} response.active Active or not
     * @apiSuccess {String} response.last_login_date Last login date of the user
     * @apiSuccess {String} response.created_date Creation date of the user
     * @apiSuccess {String} response.modification_date Modification date of the user
     * @apiSuccess {String} response.deleted_date Deleted date of the user
     *
     * @apiError {String} status error
     * @apiError {Number} code Code of the error
     * @apiError {String} response Error messsage
     */

    /**
     * @api {post} /user/login User Login
     * @apiName Login
     * @apiDescription Log user and return an access token
     * @apiGroup User
     *
     * @apiParam {String} email Email
     * @apiParam {String} password Password
     *
     * @apiSuccess {String} status status of the response
     * @apiSuccess {Number} code Code of the response
     * @apiSuccess {string} response A token
     *
     * @apiError {String} status error
     * @apiError {Number} code Code of the error
     * @apiError {String} response Error messsage
     */

    /**
     * @api {post} /user/resetpassword Reset password
     * @apiName ResetPassword
     * @apiDescription Reset password
     * @apiGroup User
     *
     * @apiParam {String} email Email
     *
     * @apiSuccess {String} status status of the response
     * @apiSuccess {Number} code Code of the response

     *
     * @apiError {String} status error
     * @apiError {Number} code Code of the error
     * @apiError {String} response Error messsage
     */


    /**
     * @api {post} /user Create a new user account
     * @apiName CreateUser
     * @apiGroup User
     *
     * @apiParam {String} email Email
     * @apiParam {String} username Username of the user
     * @apiParam {String} firstname Firstname of the user
     * @apiParam {String} lastname Lastname of the user
     * @apiParam {String} email Email of the user
     * @apiParam {String} password Password of the user
     * @apiParam {String} description Description of the user
     * @apiParam {String} city City of the user
     * @apiParam {Number} country_id Country id of the user
     * @apiParam {String} picture Picture of the user

     *
     * @apiSuccess {String} status status of the response
     * @apiSuccess {Number} code Code of the response
     * @apiSuccess {Number} response.id Id of the user
     * @apiSuccess {String} response.username Username of the user
     * @apiSuccess {String} response.firstname Firstname of the user
     * @apiSuccess {String} response.lastname Lastname of the user
     * @apiSuccess {String} response.email Email of the user
     * @apiSuccess {String} response.password Password of the user
     * @apiSuccess {String} response.description Description of the user
     * @apiSuccess {String} response.city City of the user
     * @apiSuccess {Number} response.country_id Country id of the user
     * @apiSuccess {String} response.picture Picture of the user
     * @apiSuccess {Boolean} response.active Active or not
     * @apiSuccess {String} response.last_login_date Last login date of the user
     * @apiSuccess {String} response.created_date Creation date of the user
     * @apiSuccess {String} response.modification_date Modification date of the user
     * @apiSuccess {String} response.deleted_date Deleted date of the user
     *
     *
     * @apiError {String} status error
     * @apiError {Number} code Code of the error
     * @apiError {String} response Error messsage
     */

    /**
     * @api {put} /user/:id Update user account info
     * @apiName UpdateUser
     * @apiGroup User
     * @apiHeader {String} X-Access-Token User unique access-token:  "Bearer{YOUR_TOKEN}".
     *
     * @apiParam {String} email Email
     * @apiParam {String} username Username of the user
     * @apiParam {String} firstname Firstname of the user
     * @apiParam {String} lastname Lastname of the user
     * @apiParam {String} email Email of the user
     * @apiParam {String} password Password of the user
     * @apiParam {String} description Description of the user
     * @apiParam {String} city City of the user
     * @apiParam {String} country_id Country id of the user
     * @apiParam {String} picture Picture of the user

     *
     * @apiSuccess {String} status status of the response
     * @apiSuccess {Number} code Code of the response
     * @apiSuccess {Number} response.id Id of the user
     * @apiSuccess {String} response.username Username of the user
     * @apiSuccess {String} response.firstname Firstname of the user
     * @apiSuccess {String} response.lastname Lastname of the user
     * @apiSuccess {String} response.email Email of the user
     * @apiSuccess {String} response.password Password of the user
     * @apiSuccess {String} response.description Description of the user
     * @apiSuccess {String} response.city City of the user
     * @apiSuccess {Number} response.country_id Country id of the user
     * @apiSuccess {String} response.picture Picture of the user
     * @apiSuccess {String} response.active Active or not
     * @apiSuccess {String} response.last_login_date Last login date of the user
     * @apiSuccess {String} response.created_date Creation date of the user
     * @apiSuccess {String} response.modification_date Modification date of the user
     * @apiSuccess {String} response.deleted_date Deleted date of the user
     *
     *
     * @apiError {String} status error
     * @apiError {Number} code Code of the error
     * @apiError {String} response Error messsage
     */
}
