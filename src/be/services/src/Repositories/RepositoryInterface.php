<?php

namespace PROJECT\Services;

interface RepositoryInterface
{
    /**
     * Get an object from a query.
     *
     * @param string $query A query
     * @return null
     **/
    public function get($query);

    /**
     * Get a list of objects from a query.
     *
     * @param string $query A query
     * @return null
     **/
    public function getList($query);

    /**
     * Create an object from a query.
     *
     * @param string $query A query
     * @return null
     **/
    public function create($query);

    /**
     * Update an object from a query.
     *
     * @param string $query A query
     * @return null
     **/
    public function update($query);

    /**
     * Delete an object from a query.
     *
     * @param string $query A query
     * @return null
     **/
    public function delete($query);
}
