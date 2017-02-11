<?php

/* ---------------------------------------------------------
 * src/be/models/entities/User.php
 *
 * A user.
 *
 * Copyright 2015 - PROJECT
 * ---------------------------------------------------------*/

namespace PROJECT\Models\Entities

{
    /**
     * A user.
     **/
    class Country extends BaseEntity
    {
        /**
         * @var $id
         */
        public $id;

        /**
         * @var $code
         */
        public $code;


        /**
         * @var $name
         */
        public $name;

        /**
         * @var array $fields A fields definition
         **/
        public static $settableFields = [
            'id' => [],
            'code' => [],
            'name' => []
        ];
    }
}
