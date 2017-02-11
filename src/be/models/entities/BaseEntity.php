<?php

/* ---------------------------------------------------------
 * src/be/models/entities/BaseEntity.php
 *
 * A base entity.
 *
 * Copyright 2015 - PROJECT
 * ---------------------------------------------------------*/

namespace PROJECT\Models\Entities

{
    use PROJECT\Exceptions;

    /**
     * A base entity.
     **/
    class BaseEntity
    {
        /**
         * @var array $settableFields A fields definition
         **/
        public static $settableFields = [];

        /**
         * Constructor.
         *
         * @param array $fields An array of fields of the form columnName => value
         **/
        public function __construct($fields = [])
        {
            $fields = $fields ? $fields : [];
            $this->validateFields($fields);
            $this->setFields($fields);
            $this->setDefaults();
            $this->applyConstructors();
        }

        /**
         * Assert that the $fields array contains all required fields
         *
         * @param array $fields An array of fields of the form columnName => value
         * @return boolean if $fields is valid, throws an exception otherwise
         **/
        public function validateFields($fields)
        {
            foreach (static::$settableFields as $field => $options) {
                if (isset($options['required']) && $options['required'] && !array_key_exists($field, $fields)) {

                    throw new Exceptions\BadRequestException("Required field => `$field`");
                }
            }

            return true;
        }

        /**
         * Set each propery of the address contained in $fields
         *
         * @param array $fields An array of fields of the form columnName => value
         **/
        public function setFields($fields)
        {
            foreach ($fields as $field => $value) {
                if (is_string($value)) {
                    $value = preg_replace('/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]|[\x00-\x7F][\x80-\xBF]+|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/S', '?', $value);
                    $value = preg_replace('/\xE0[\x80-\x9F][\x80-\xBF]|\xED[\xA0-\xBF][\x80-\xBF]/S', '?', $value);
                }
                $this->$field = $value;
            }
        }

        /**
         * Set the default values for unset fields
         **/
        public function setDefaults()
        {
            foreach (static::$settableFields as $field => $options) {
                if (!isset($this->$field ) && isset( $options['default'])) {
                    $this->$field = $options['default'];
                }
            }
        }

        public function isConstructorFunction($funcName)
        {
            return method_exists($this, $funcName);
        }
        public function constructEntity($constructor, $data)
        {
            return $this->isConstructorFunction($constructor)
                ? $this->{$constructor}($data)
                : $data instanceof $constructor
                    ? $data
                    : new $constructor($data);
        }
        public function applyConstructors()
        {
            foreach (static::$settableFields as $field => $options) {
                if (isset($options['constructor'])) {
                    $constructor = $options['constructor'];
                    if (isset($options['isArray']) && $options['isArray']) {
                        $constructedItems = [];
                        foreach ($this->$field as $item) {
                            $constructedItems[] = $this->constructEntity($constructor, $item);
                        }
                        $this->$field = $constructedItems;
                    } else {
                        $this->$field = $this->constructEntity($constructor, $this->$field);
                    }
                }
            }
        }


        /**
         * Set the default values for unset fields
         **/
        public function getFields()
        {
            $gettableFields = [];
            foreach (static::$gettableFields as $field => $defaultValue) {
                $value = $defaultValue;
                if (!is_null($this->{$this->getPropertyMethod($field)}())) {
                    $value = $this->{$this->getPropertyMethod($field)}();
                }
                $gettableFields[$field] = $value;
            }
            return $gettableFields;
        }


        /**
         * Returns the corresponding get method for a given property.
         * Ex. will turn 'base_price' into getBasePrice
         *
         * @param string $name property name
         * @return string Corresponding applyFilter method
         */
        private function getPropertyMethod($name)
        {
            $splitNames = explode('_', $name);

            foreach ($splitNames as $splitName) {
                $camelName[] = ucwords($splitName);
            }
            // Turn string into words to properly capitalize and then concatenate each word
            $capitalizedName = implode($camelName);

            return 'get' . $capitalizedName;
        }

        public function getConstraints() {
            return static::$constraints;
        }
    }
}
