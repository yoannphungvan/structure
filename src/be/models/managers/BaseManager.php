<?php

/* ---------------------------------------------------------
 * src/be/models/managers/BaseManager.php
 *
 * A base manager.
 *
 * Copyright 2015 - PROJECT
 * ---------------------------------------------------------*/

namespace PROJECT\Models\Managers {

    use PROJECT\Models\Managers\AppProxy;
    use PROJECT\Exceptions;

    /**
     * A base manager.
     **/
    abstract class BaseManager
    {
        /**
         * @var ModelManagerFactory
         **/
        public $managerFactory;

        /**
         * @var Repository $repo A repository
         **/
        public $repo;

        /**
         * @var Object $cache A cache
         **/
        public $cache;

        /**
         * @var string $table A table
         **/
        protected $table;

        /**
         * @var string $entity An entity
         **/
        protected $entity;

         /**
         * @var Object $filters A set of filters
         **/
        protected $filters;

        /**
         * Holds the proper filter class to instantiate
         * Overwrite it in subclass to instantiate a specific filter class
         *
         * @var String
         */
        protected $filterClass = "Filters";

        /**
         * Prefix table
         * @var string $prefixTableName
         */
        protected $prefixTableName = null;

        /**
         * Array of query builders
         * @var Array of QueryBuilder $query
         */
        protected $query = array();

        /**
         * Array of select parameters
         * @var Array  $select
         */
        protected $select = array();

        /**
         * Array of where clauses
         * @var Array $where
         */
        protected $where = array();

        /**
         * Array of order
         * @var Array $order
         */
        protected $orderBy = array();

        /**
         * @var Object $app A Silex App
         * */
        public $app;


        const QUERY_LIST_NAME = 'List';

        const QUERY_ONE_NAME = 'One';

        /**
         * Constructor.
         *
         * @param Repository $repo A repository
         * @param Object $cache A cache
         * @param ModelManagerFactory $managerFactory A manager factory
         **/
        public function __construct($repo = null, $cache = null, $managerFactory = null, $filters = array())
        {
            $this->app = AppProxy::getInstance()->getApp();

            if (!$repo) {
                $this->repo = $this->app;
            } else {
                $this->repo = $repo;
            }

            if (!$cache) {
                $this->cache = $this->app['predis'];
            } else {
                $this->cache = $cache;
            }
            if ($managerFactory) {
                $this->managerFactory = $managerFactory;
            }

            $filterClass   = $this->getFiltersClass();
            $this->filters = new $filterClass($filters);
            $this->setFilters($filters);
        }

        protected function getFiltersClass()
        {
            return 'PROJECT\\Services\\Filters\\' . $this->filterClass;
        }

        /**
         * Set Filters
         *
         * @param Object $object An Object instance
         **/
        public function setFilters($filters)
        {
            $this->filters = $this->filters->build($filters);

            return $this->filters;
        }

        /**
         * Get Filters
         *
         * @return Object $filters An Object filters
         **/
        public function getFilters()
        {
            return $this->filters;
        }

        /**
         * Get a list of objects.
         *
         * @param array $filters An array of filters
         * @param integer $page A page number
         * @param integer $perPage A number of objects per page
         * @return Object An array of object instances
         **/
        public function getList($filters, $page = null, $perPage = null, $returnOne = false)
        {
            $queryBuilder = $this->repo->getQueryBuilder();

            $queryBuilder
                ->select('m.*')
                ->from($this->table, 'm');

            if (!is_array($filters)) {
                 $filters = $filters->toArray();
            }

            if (isset($filters) && sizeof($filters) > 0) {
                $andClause = $this->buildAndClause($queryBuilder, 'm', $filters);
                $queryBuilder->where($andClause);
            }

            if (!empty($perPage) && !empty($page)) {
                $queryBuilder
                    ->setMaxResults($perPage)
                    ->setFirstResult($page * $perPage);
            }

            if ($returnOne) {
                $result = $this->repo->get($queryBuilder);
                if (!empty($result)) {
                    $objects = new $this->entity($result);
                } else {
                    $objects = null;
                }
            } else {
                $results = $this->repo->getList($queryBuilder);
                $objects = array();
                foreach ($results as $result) {
                    $objects[] = new $this->entity($result);
                }
            }

            return $objects;
        }

        /**
         * Get one object.
         *
         * @param array $filters An array of filters
         * @return Object An array of object instances
         **/
        public function getOne($filters){
            return $this->getList($filters, null, null, true);
        }

        /**
         * Get an object from an id.
         *
         * @param integer $id an object id
         * @param Array $filters An array of filters
         * @return Object an object instance
         **/
        public function getById($id, $filters = array())
        {
            $queryBuilder = $this->repo->getQueryBuilder();

            $queryBuilder
                ->select('m.*')
                ->from($this->table, 'm');

            $filters['id'] = $id;
            $andClause = $this->buildAndClause($queryBuilder, 'm', $filters);

            $queryBuilder
                ->where($andClause)
                ->setMaxResults(1)
                ->setFirstResult(0);

            $result = $this->repo->get($queryBuilder);

            if (!isset($result)) {
                throw new Exceptions\ResourceNotFoundException('Not data found for id:' . $id);
            }
            $object = new $this->entity($result);

            return $object;
        }

        /**
         * Build a And clause.
         *
         * @param QueryBuilder $queryBuilder A query builder
         * @param Array $filters An array of filters
         * @param Object An and clause
         **/
        public function buildAndClause($queryBuilder, $prefix, $filters, $andClause = false)
        {
            if (method_exists($filters, 'toArray')) {
                $filters = $filters->toArray();
            }
            if (!$andClause) {
                $andClause = $queryBuilder->expr()->andx();
            }

            foreach ($filters as $name => $value) {
                switch ($name) {
                    case 'dateCreation':
                        $andClause->add($queryBuilder->expr()->gt($prefix . '.' . $name, $queryBuilder->expr()->literal($value)));
                        break;
                    case 'publicationDate':
                        $andClause->add($queryBuilder->expr()->orX($queryBuilder->expr()->isNull($prefix . '.' . $name), $queryBuilder->expr()->lte($prefix . '.' . $name, $queryBuilder->expr()->literal($value))));
                        break;
                    case 'expirationDate':
                        $andClause->add(
                            $queryBuilder->expr()->orX(
                                $queryBuilder->expr()->isNull($prefix . '.' . $name),
                                $queryBuilder->expr()->gte($prefix . '.' . $name, $queryBuilder->expr()->literal($value)),
                                $queryBuilder->expr()->eq($prefix . '.' . $name, $queryBuilder->expr()->literal('0000-00-00 00:00:00'))
                            )
                        );
                        break;
                    default:
                        $andClause->add($queryBuilder->expr()->eq($prefix . '.' . $name, $queryBuilder->expr()->literal($value)));
                        break;
                }
            }

            return $andClause;
        }

        /**
         * Create an object.
         *
         * @param array $fields An array of columnName => value
         * @return Object An object instance
         **/
        public function create($fields)
        {
            $object = new $this->entity($fields);
            return $object;
        }

        /**
         * Persist an object.
         *
         * @param Object $object An object instance
         **/
        public function persist($object)
        {
            $params = $this->setParams($object);

            $queryBuilder = $this->repo->getQueryBuilder();

            if (isset( $object->id )) {
                $queryBuilder
                    ->update($this->table)
                    ->where($queryBuilder->expr()->eq('id', $queryBuilder->expr()->literal($object->id)));

                foreach ($params as $name => $value) {
                    if (!is_null($value)) {
                        $queryBuilder->set($name, $queryBuilder->expr()->literal($value));
                    } else {
                        $queryBuilder->set($name, 'NULL');
                    }
                }

                $this->repo->update($queryBuilder);
            } else {
                $queryBuilder->insert($this->table);

                foreach ($params as $name => $value) {
                    if ($value) {
                        $queryBuilder->setValue($name, $queryBuilder->expr()->literal($value));
                    }
                }
                return $this->repo->create($queryBuilder);
            }
        }

        /**
         * Delete an object.
         *
         * @param Object $object An object instance
         **/
        public function delete($object)
        {
            $queryBuilder = $this->repo->getQueryBuilder();

            $queryBuilder
                ->delete($this->table)
                ->where($queryBuilder->expr()->eq('id', $queryBuilder->expr()->literal($object->id)));

            $this->repo->delete($queryBuilder);
        }

        /**
         * Get the settable fields of an address.
         *
         * @return array An associative array of the form column_name => value
         **/
        public function getSettableFields()
        {
            $entity = $this->entity;

            return $entity::$settableFields;
        }

        /**
         * Update an object instance to prepare it for persistance
         *
         * @param Object $object An object instance
         * @param array $fields An array of fields to update
         * @return Object An updated object instance
         **/
        public function prepareUpdate($object, $fields)
        {
            foreach ($fields as $field => $value) {
                $object->$field = $value;
            }

            return $object;
        }

        /**
         * Creates the parameters to be sent to the repo
         *
         * @param Object $object An Object instance
         * @return array An array of parameters, column_name => value
         **/
        public function setParams($object)
        {
            $settableFields = $this->getSettableFields();
            $params = [];
            foreach ($settableFields as $field => $option) {
                $params[$field] = $object->$field;
            }

            return $params;
        }

        /**
         * Get products list based on the filters provided
         *
         * @param  QueryBuilder $queryBuilder A queryBuilder
         * @param  Array $filters A reference to an array of filters
         * @param  Array $initFilters A reference to the array of initial filters
         * @param  Integer $page  The page to view
         * @param  Integer $perPage The number of item to view per page
         *
         * @return QueryBuilder $queryBuilder A queryBuilder
         */
        public function buildQuery($queryName = 'List')
        {
            $this->initManagers($queryName);
            $this->query[$queryName] = $this->repo->getQueryBuilder();
            $this->where[$queryName] = $this->query[$queryName]->expr()->andx();
            //Select
            $this->{'setSelect'.$queryName}($queryName);

            //Where clause
            $this->{'setWhere'.$queryName}($queryName);
            if ($this->where[$queryName]->count() > 0) {
                $this->query[$queryName]->where($this->where[$queryName]);
            }

            //Group By (depends on the query)
            if (method_exists($this, 'setGroupBy'.$queryName)) {
                $this->{'setGroupBy'.$queryName}($queryName);
            }
            //Order (for a listing)
            if (method_exists($this, 'setOrder'.$queryName)) {
                // FIX ME - Decide on setOrderBy or setOrder versus setGroupBy and setGroup(ing?)
                $this->{'setOrder'.$queryName}($queryName);
            }
            //Pagination (for a listing)
            if (method_exists($this, 'setPagination'.$queryName)) {
                $this->{'setPagination'.$queryName}($queryName);
            }
        }

        public function getSelectPart($queryName)
        {
            if (!isset($this->query[$queryName])) {
                 $this->query[$queryName] = $this->repo->getQueryBuilder();
            }

            $this->initManagers($queryName);

            $queryBuilder = $this->repo->getQueryBuilder();

            $froms = $this->query[$queryName]->getQueryPart('from');

            if (sizeof($froms)==0) {
                $this->{'setSelect' . $queryName}($queryName);
                $froms = $this->query[$queryName]->getQueryPart('from');
            }

            //Select
            $queryBuilder->select($this->query[$queryName]->getQueryPart('select'));

            //From
            foreach ($froms as $from) {
                $queryBuilder->from($from['table'], $from['alias']);
            }
            //Joins
            //Fix me: optimize it
            $joins = $this->query[$queryName]->getQueryPart('join');
            foreach ($joins as $alias => $join) {
                foreach ($join as $joinParams) {
                    $queryBuilder->{$joinParams['joinType'] . 'Join'}($alias, $joinParams['joinTable'], $joinParams['joinAlias'], $joinParams['joinCondition']);
                }
            }

            return $queryBuilder;
        }

        public function getWherePart($queryName)
        {
            if (!isset($this->query[$queryName])) {
                 $this->query[$queryName] = $this->repo->getQueryBuilder();
            }

            if (isset($this->where[$queryName])) {
                return clone $this->where[$queryName];
            }

            if (!isset($this->where[$queryName])) {
                $this->where[$queryName] = $this->repo->getQueryBuilder()->expr()->andx();
            }

            $this->initManagers($queryName);

            $this->{'setWhere' . $queryName}($queryName);

            // Clone the query to prevent sharing a reference across difference manager
            return clone $this->where[$queryName];
        }

        public function getQueryPart($queryPart, $queryName)
        {
            if (isset($this->query[$queryName]) && !is_null($this->query[$queryName]->getQueryPart($queryPart))) {
                return $this->query[$queryName]->getQueryPart($queryPart);
            }

            $this->buildQuery($queryName);

            return $this->query[$queryName]->getQueryPart($queryPart);
        }

        protected function createObject($queryName)
        {
        }

        /**
          * Get product count given a list of filters
          *
          * @param QueryBuilder $queryBuilder A queryBuilder
          * @param Array $filters An array of filters
          *
          * @return $products A list of products matching the criteria
          */

        protected function getObjectsListFromQuery($queryName)
        {
            $objectInfos = $this->repo->getList($this->query[$queryName]);
            $objects = array();
            foreach ($objectInfos as $objectInfo) {
                $objects[] = $this->createObject($objectInfo);
            }
            return $objects;
        }

        protected function getObjectFromQuery($queryName)
        {
            $objectInfo = $this->repo->get($this->query[$queryName]);
            return $this->createObject($objectInfo);
        }

        protected function setPaginationList($queryName)
        {
            $page = $this->filters->page;
            $perPage = $this->filters->per_page;

            $this->query[$queryName]->setFirstResult($page * $perPage);
            $this->query[$queryName]->setMaxResults($perPage);
        }

        protected function setWhereList($queryName)
        {
            if (array_key_exists($queryName, $this->where) && $this->where[$queryName]->count() > 0) {
                return $this->query[$queryName]->where($this->where[$queryName]);
            }

            $this->getWhereList($queryName);
        }

        protected function getWhereList($queryName)
        {
            $this->applyFilters($queryName);
            $this->applyCustomFilters($queryName);
            if (method_exists($this, 'applyCustomWhereSql'.$queryName)) {
                $this->{'applyCustomWhereSql'.$queryName}($queryName);
            }
            return $this->where[$queryName];
        }

        protected function applyFilters($queryName)
        {
            $simpleFilters = $this->filters->getSimple();
            foreach ($simpleFilters as $filter) {
                $this->applySimpleFilter($queryName, $filter);
            }
        }

        protected function applySimpleFilter($queryName, $filter)
        {
            // Call the query builder with the given operator
            $op = $filter['operator'];
            $prefix = array_key_exists('prefix', $filter) ? $filter['prefix'] : $this->prefixTableName;
            //FIX ME: add the isnull and not is null operator
            $this->where[$queryName]->add($this->query[$queryName]->expr()->{$op}(
                $prefix . $filter['name'],
                $this->query[$queryName]->expr()->literal($filter['value'])
            ));
        }

        /**
         * For each custom filter, we call the related method to apply filter logic
         *
         */
        protected function applyCustomFilters($queryName)
        {
            foreach ($this->filters->toArray() as $name => $filter) {
                if ($filter['operator'] == 'custom') {
                    $methodName = $this->getApplyFilterMethod($name);
                    if (method_exists($this, $methodName.$queryName)) {
                        $this->{$methodName.$queryName}($queryName);
                    }
                }
            }
        }

        /**
         * Returns the corresponding applyFilter method for a given method.
         * Ex. will turn 'category_id' into applyFilterCategoryId
         *
         * @param string $name Filter's name
         * @return string Corresponding applyFilter method
         */
        private function getApplyFilterMethod($name)
        {
            $splitNames = explode('_', $name);

            foreach ($splitNames as $splitName) {
                $camelName[] = ucwords($splitName);
            }
            // Turn string into words to properly capitalize and then concatenate each word
            $capitalizedName = implode($camelName);

            return 'applyFilter' . $capitalizedName;
        }


        /**
         * Initialize Managers
         * @param  string $queryName query name
         */
        protected function initManagers($queryName)
        {
        }

        /**
         * get redis key name
         *
         * @return redis key name
        **/
        protected function getRedisKeyName()
        {
            return $this->objectCacheTag . ':' . $this->getRedisFiltersKeyName();
        }

        /**
         * Create a key name for a single entity like designer or category that
         * doesn't depend on any other filters to be valid.
         *
         * @param string|integer $id The db $id of the entity
         *
         * @return string The redis key to use
         */
        protected function getRedisSingleKeyName($id, $prefix = '', $sufix = '')
        {
            return $prefix . $this->objectCacheTag . ':' . $id . ($sufix != '' ? ':'. $sufix:'');
        }

        /**
         * Give the redis key part based on filters
         * @return string    Part of the redis key name
         */
        protected function getRedisFiltersKeyName()
        {
            $redisKeyElements = array();

            foreach ($this->filters->toArray() as $key => $filter) {
                $redisKeyElements[] = $key;
                $redisKeyElements[] = $filter['value'];
            }

            return implode(':', $redisKeyElements);
        }


        public function validation($object)
        {
            $errors = [];
            $entity = new $this->entity();

            if (method_exists($entity, 'getConstraints')) {
                foreach ($entity->getConstraints() as $key => $constraints) {
                    $value = $object->{$key};
                    foreach ($constraints as $constraint => $val) {
                        switch($constraint){
                            case 'Required' :
                                if (empty($value)) {
                                    $errors[] = $key . ' is missing';
                                }
                                break;
                            case 'Numeric' :
                                if (!is_numeric($value)) {
                                    $errors[] = $key . ' must be numeric';
                                }
                                break;
                            case 'Email' :
                                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                                    $errors[] = $key . ' must be a valid email address';
                                }
                                break;
                            case 'Regex' :
                                if (!preg_match($val, $value)) {
                                    $errors[] = $key . ' is not valid';
                                }
                                break;
                        }
                    }
                }
            }

            return $errors;
        }
    }
}
