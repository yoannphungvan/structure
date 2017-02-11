<?php

/* ---------------------------------------------------------
 * src/be/models/entities/Word.php
 *
 * A word.
 *
 * Copyright 2015 - PROJECT
 * ---------------------------------------------------------*/

namespace PROJECT\Models\Entities;

/**
 * A word.
 **/
class Word extends BaseEntity
{
    /**
     * @var $id
     */
    public $id;

    /**
     * @var $name
     */
    public $name;

    /**
     * @var $created_date
     */
    public $created_date;

    /**
     * @var array $fields A fields definition
     **/
    public static $settableFields = [
        'id'  => [],
        'name'  => [],
        'created_date'  => [],
    ];

    /**
     * @var array $fields A fields definition
     **/
    public static $gettableFields = [
        'id'  => [],
        'name'  => [],
        'created_date'  => [],
    ];
}

