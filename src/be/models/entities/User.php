<?php

/* ---------------------------------------------------------
 * src/be/models/entities/User.php
 *
 * A user.
 *
 * Copyright 2015 - PROJECT
 * ---------------------------------------------------------*/

namespace PROJECT\Models\Entities;

/**
 * A user.
 **/
class User extends BaseEntity
{
    /**
     * @var $id
     */
    public $id;

    /**
     * @var $username
     */
    public $username;

    /**
     * @var $firstname
     */
    public $firstname;

    /**
     * @var $lastname
     */
    public $lastname;

    /**
     * @var $email
     */
    public $email;

    /**
     * @var $password
     */
    public $password;

    /**
     * @var $password_token
     */
    public $password_token;

    /**
     * @var $description
     */
    public $description;

    /**
     * @var $city
     */
    public $city;

    /**
     * @var $country_id
     */
    public $country_id;

    /**
     * @var $picture
     */
    public $picture;

    /**
     * @var $active
     */
    public $active;

    /**
     * @var $last_login_date
     */
    public $last_login_date;

    /**
     * @var $created_date
     */
    public $created_date;

    /**
     * @var $modification_date
     */
    public $modification_date;

    /**
     * @var $deleted_date
     */
    public $deleted_date;

    /**
     * @var array $fields A fields definition
     **/
    public static $settableFields = [
        'id'  => [],
        'username'  => [],
        'firstname'  => [],
        'lastname'  => [],
        'email'  => [],
        'password'  => [],
        'password_token'  => [],
        'description'  => [],
        'city'  => [],
        'country_id'  => [],
        'picture'  => [],
        'active'  => [],
        'last_login_date'  => [],
        'created_date'  => [],
        'modification_date'  => [],
        'deleted_date'  => []
    ];

    /**
     * @var array $fields A fields definition
     **/
    public static $gettableFields = [
        'id'  => [],
        'username'  => [],
        'firstname'  => [],
        'lastname'  => [],
        'email'  => [],
        'password'  => [],
        'password_token'  => [],
        'description'  => [],
        'city'  => [],
        'country_id'  => [],
        'picture'  => [],
        'active'  => [],
        'last_login_date'  => [],
        'created_date'  => [],
        'modification_date'  => [],
        'deleted_date'  => []
    ];

    public static $constraints = [
        'username'          => ['Required' => true, 'Regex' => '/[a-zA-Z0-9_]{6,20}/'],
        'firstname'         => ['Required' => true],
        'lastname'          => ['Required' => true],
        'email'             => ['Required' => true, 'Email' => true],
        'password'          => ['Regex' => '/[a-zA-Z0-9_\.]{6,100}/'],
        'description'       => [],
        'city'              => [],
        'country_id'        => ['Numeric' => true],
        'picture'           => [],
        'active'            => [],
        'last_login_date'   => [],
        'created_date'      => [],
        'modification_date' => [],
        'deleted_date'      => []
    ];
}

