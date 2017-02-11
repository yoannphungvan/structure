<?php
/* ---------------------------------------------------------
 * src/be/services/MySQLRepository.php
 *
 * A MySQL repository.
 *
 * Copyright 2015 - PROJECT
 * --------------------------------------------------------- */

namespace PROJECT\Services;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * A MySQL repository
 * */
class MySQL implements RepositoryInterface
{
    /**
     * @var Connection $connection A DB Connection
     * */
    protected $connection;

    /**
     * @var Configuration $configuration A DB Configuration
     * */
    protected $configuration;

    /**
     * Constructor.
     *
     * @param string $host A host
     * @param string $username A username
     * @param string $password A password
     * @param string $db A database
     * @param string $port A port
     **/
    public function __construct($host, $username, $password, $db, $port, $slavesConfig = null)
    {
        $this->configuration = new Configuration();

        // Default master mysql connection
        $connectionParams = [
            'dbname' => $db,
            'user' => $username,
            'password' => $password,
            'host' => $host,
            'port' => $port,
            'driver' => 'pdo_mysql',
            'charset' => 'utf8'
        ];

        // Slave mysql connection if slaves are defined
        if (isset($slavesConfig)) {
            $connectionParams = [
              'wrapperClass' => 'Doctrine\DBAL\Connections\MasterSlaveConnection',
              'driver' => 'pdo_mysql',
              'master' => $connectionParams,
              'slaves' => $slavesConfig
            ];
        }
        $this->connection = DriverManager::getConnection($connectionParams, $this->configuration);
    }

    /**
     * Destructor.
     * */
    public function __destruct()
    {
        $this->connection->close();
    }

    /**
     * Get a query builder instance
     * */
    public function getQueryBuilder()
    {
        return $this->connection->createQueryBuilder();
    }

    /**
     * Get the connection object for running prepared statements.
     *
     * @return Doctrine\DBAL\Connection The connection object.
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Execute a query
     *
     * @param QueryBuilder $queryBuilder A query builder
     * @return Statement a statement
     **/
    public function execute(QueryBuilder $queryBuilder)
    {
        $query = $queryBuilder->getSql();

        $statement = $queryBuilder->execute($query);

        return $statement;
    }

    /**
      * Accessor function to run raw prepared queries if required.
      *
      * @param   string  $sql The raw sql string
      * @param   array  $params  Array of paramaters to replace in the query.
      *
      * @return  array Returns the rows returned from the query.
      */
    public function executeQuery($sql, $params = [], $types = [])
    {
        return $this->connection->executeQuery($sql, $params)->fetchAll();
    }

    /**
      * Accessor fucntion to run executeUpdate directly on the connection
      * with raw sql.
      *
      * @param string $sql The sql string to run with possible place holders.
      * @param array $params Array of parameters for positional or named args in the query.
      * @param array $types DBAL types for the provied arguments.
      *
      * @return integer The number of affected rows.
      */
    public function executeUpdate($sql, $params = [], $types = [])
    {
        return $this->connection
        ->executeUpdate($sql, $params, $types);
    }

    /**
     * Get an object from a query.
     *
     * @param QueryBuilder $query A query
     * @return mixed A result
     * */
    public function get($query)
    {
        $statement = $this->execute($query);
        $results   = $statement->fetchAll();

        if (sizeof($results) > 0) {
            return $results[0];
        } else {
            return null;
        }
    }

    /**
     * Get a list of objects from a query.
     *
     * @param string $query A query
     * @return mixed A result
     **/
    public function getList($query)
    {
        $statement = $this->execute($query);

        $results = $statement->fetchAll();

        return $results;
    }

    /**
     * Create an object from a query.
     *
     * @param string $query A query
     * @return integer An id
     * */
    public function create($query)
    {
        $this->execute($query);

        return $this->connection->lastInsertId();
    }

    /**
     * Update an object from a query.
     *
     * @param string $query A query
     **/
    public function update($query)
    {
        $this->execute($query);
    }

    /**
     * Delete an object from a query.
     *
     * @param string $query A query
     **/
    public function delete($query)
    {
        $this->execute($query);
    }

    /**
     * Ping
     *
     * @return boolean
     **/
    public function ping()
    {
        return $this->connection->ping();
    }
}
