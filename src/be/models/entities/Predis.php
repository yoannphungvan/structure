<?php
/* ---------------------------------------------------------
 * src/be/models/entities/Predis.php
 *
 * A Predis entity.
 *
 * Copyright 2014 - SSENSE
 * --------------------------------------------------------- */

namespace PROJECT\Models\Entities;

use Silex\Application;
use \Predis\Client as PredisClient;

/**
 * A Predis entity singleton.
 * */
class Predis
{
    const COMPRESSED_KEY_PREFIX = 'gz_';

    /**
     * Private reference to current instance of app.
     *
     * @var  Application
     */
    private static $cache = null;

    // Protect the constructor and magic methods to make
    // this a true singleton.
    private function __construct($host, $port, $scheme)
    {
        $client      = new PredisClient([
            'scheme' => $scheme,
            'host' => $host,
            'port' => $port,
        ]);
        self::$cache = $client;
    }

    private function __clone()
    {

    }

    private function __wakeup()
    {

    }

    /**
     * Returns the current instance of the singleton.
     *
     * @param string $host IP of the Redis server
     * @param string $port Port
     * @param string $scheme Default is tcp
     * @return  Predis  The current singleton instance.
     */
    public static function getInstance($host, $port, $scheme = 'tcp')
    {
        /**
         * @var Singleton $_instance
         */
        static $_instance = null;

        if ($_instance === null) {
            $_instance = new Predis($host, $port, $scheme);
        }

        return $_instance;
    }

    /**
     * Function to initialize the cache client the class uses to talk to redis.
     *
     * @param  PredisClient $cache Instance of \Predis\Client to use for caching calls.
     */
    public static function setCacheClient(\Predis\Client $cache)
    {
        self::$cache = $cache;
    }

    /**
     * Add gz compressed prefix to key if not already present
     *
     * @param string $key Identifier
     * @return string     compress prefixed key
     */
    private function addCompressedPrefix($key)
    {
        if (!preg_match('/^' . self::COMPRESSED_KEY_PREFIX . '/', $key)) {
            $key = self::COMPRESSED_KEY_PREFIX . $key;

        }

        return $key;
    }

    /**
     * Save a key in the cache
     *
     * @param Application $app Application instance
     * @param string $key      Identifier
     * @param string $value    Value to store in the cache
     * @param int $ttl         Time to live in second. If null, take the default one define in services.php
     */
    public function set($key, $value = null, $ttl = null)
    {
        if ((!is_string($value) && !is_numeric($value)) && !self::isSerialized($value)) {
            $value = serialize($value);
        }

        // All new keys use the compressed key format.
        $key = $this->addCompressedPrefix($key);
        self::$cache->set($key, gzcompress($value));

        $this->setExpire($key, $ttl);
    }

    /**
     * Set TTL on a key to set it's expire timeout
     *
     * @param string $key Identifier
     * @param int $ttl    Time to live in second. If null, take the default one
     */
    private function setExpire($key, $ttl)
    {
        if (!$ttl || !is_numeric($ttl)) {
            $ttl = 5 * 3600;
        }

        self::$cache->expire($key, $ttl);
    }

    /**
     * Get the content of the cache based on the key
     *
     * @param  object $cache Can be either an Application isntance or a Predis client instance
     * @param  string $key   Cache key identifier
     * @return string
     */
    public function get($key)
    {
        $key = $this->checkKeyExists($key);
        if (!$key) {
            return null;
        }

        $data = self::$cache->get($key);
        if (strpos($key, self::COMPRESSED_KEY_PREFIX) !== false) {
            $data = gzuncompress($data);
        }

        if (self::isSerialized($data)) {
            return unserialize($data);
        }

        return $data;
    }

    /**
     *  Check to see if the key exists and init it to $value if it doesn't,
     *  Otherwise redis wil bitch and die when incr a non-existant key.
     *
     * @param string $key Identifier
     * @param int $value  Initial value to set the key to
     */
    private function setInitialIncrementVal($key, $value = 0)
    {
        if (!self::$cache->exists($key)) {
            self::$cache->set($key, $value);
        }
    }

    /**
     * Increment a key by a given value in the cache
     *
     * @param string $key    Identifier
     * @param int $value     Value to increment key by
     * @param int $initalVal Initial value to set if key not exist yet
     */
    public function incrby($key, $value, $ttl = null, $initialVal = 0)
    {
        $this->setInitialIncrementVal($key, $initialVal);

        self::$cache->incrby($key, $value);

        $this->setExpire($key, $ttl);
    }

    /**
     * Decrement a key by a given value in the cache
     *
     * @param string $key    Identifier
     * @param int $value     Value to decrement key by
     * @param int $initalVal Initial value to set if key not exist yet
     */
    public function decrby($key, $value, $ttl = null, $initialVal = 0)
    {
        $this->setInitialIncrementVal($key, $initialVal);

        self::$cache->decrby($key, $value);

        $this->setExpire($key, $ttl);
    }

    /**
     * Save a key in a redis list
     *
     * @param Application $app Application instance
     * @param string $key      Identifier
     * @param string $value    Value to store in the cache
     * @param int $ttl         Time to live in second. If null, take the default one define in services.php
     */
    public function rpush($key, $value = null, $ttl = null)
    {

        self::$cache->rpush($key, $value);
        // set expire

        $this->setExpire($key, $ttl);
    }

    /**
     * Get the content of the redis list based on the key
     *
     * @param  object $cache  Can be either an Application isntance or a Predis client instance
     * @param  string $key    Cache key identifier
     * @param  integer $start start index
     * @param  integer $stop  stop index (-1 is the last element of the list)
     * @return string
     */
    public function lrange($key, $start, $stop)
    {
        $key = $this->checkKeyExists($key);
        if (!$key) {
            return null;
        }

        return self::$cache->lrange($key, $start, $stop);
    }

    /**
     * Delete cache based on the key
     *
     * @param  object $cache Can be either an Application isntance or a Predis client instance
     * @param  string $key   Cache key identifier
     * @return void
     */
    public function delete($key)
    {
        $key = $this->checkKeyExists($key);
        if (!$key) {
            return false;
        }

        return self::$cache->del($key);
    }

    /**
     * Function to obtain the correct key name while
     * we're in transition between compressed keys and regular keys
     * Returns the name of the proper key or false if no such key exists.
     *
     * @param string $key The regular key name
     * @return string|boolean Returns the keyname to use or false if it doesn't exist
     *                    in the db.
     */
    private function checkKeyExists($key)
    {
        $compressedKey = $this->addCompressedPrefix($key);

        // Always check for the compressed key first to speed
        // up the transition to compressed keys.
        if (self::$cache->exists($compressedKey)) {
            // delete the old key (non gz prefix key)
            if ($key != $compressedKey && self::$cache->exists($key)) {
                // can't use $this->delete here as it introduces an
                // infinite loop.
                self::$cache->del($key);
            }

            return $compressedKey;
        }

        if (self::$cache->exists($key)) {
            return $key;
        }

        return false;
    }

    /**
     * Expire function to manually set TTL's on keys.
     *
     * @param   string $key  The key to set a TTL on.
     * @param   integer $ttl The TTL in seconds
     * @return  object        Returns the client's response.
     */
    public function expire($key, $ttl)
    {
        $key = $this->checkKeyExists($key);
        if (!$key) {
            return false;
        }

        return self::$cache->expire($key, $ttl);
    }

    /**
     * Public wrapper around checkKeyExists for code still using exists()
     *
     * @param   string $key The key to check
     *
     * @return  boolean Returns boolean result.
     */
    public function exists($key)
    {
        return $this->checkKeyExists($key) ? true : false;
    }

    /**
     * Get list of keys based on a pattern
     *
     * @param  object $cache Can be either an Application isntance or a Predis client instance
     * @param  string $key   Cache key identifier
     * @return array
     */
    public static function getKeys($pattern = '*')
    {
        return self::$cache->keys($pattern);
    }

    /**
     * Check if a string is serialized
     *
     * @param  string $value String to check
     * @return boolean
     */
    private static function isSerialized($value)
    {
        // Bit of a give away this one
        if (empty($value) || !is_string($value) || $value == '') {
            return false;
        }

        // Serialized false, return true. unserialize() returns false on an
        // invalid string or it could return false if the string is serialized
        // false, eliminate that possibility.
        if ($value === 'b:0;') {
            return true;
        }

        $length = strlen($value);
        $end    = '';

        switch ($value[0]) {
            case 's':
                if ($value[$length - 2] !== '"') {
                    return false;
                }
            // break intentionally omitted
            case 'b':
            case 'i':
            case 'd':
                // This looks odd but it is quicker than isset()ing
                $end .= ';';
            // break intentionally omitted
            case 'a':
                // same
            case 'O':
                $end .= '}';

                if ($value[1] !== ':') {
                    return false;
                }

                switch ($value[2]) {
                    case 0:
                    case 1:
                    case 2:
                    case 3:
                    case 4:
                    case 5:
                    case 6:
                    case 7:
                    case 8:
                    case 9:
                        break;

                    default:
                        return false;
                }

            // break intentionally omitted
            case 'N':
                $end .= ';';

                if ($value[$length - 1] !== $end[0]) {
                    return false;
                }

                break;

            default:
                return false;
        }

        if (@unserialize($value) === false) {
            return false;
        }

        return true;
    }
}
