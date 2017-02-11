<?php
/**
 *
 * Copyright 2015 - SSENSE
 */

namespace PROJECT\Exceptions;

class ServiceUnavailableException extends BaseException
{
    const ERROR_CODE = 503;

    /**
     * Constructor
     *
     * @param  string  $message     The msg key for translation or straight up message.
     */
    public function __construct($message = '')
    {
        parent::__construct($message, self::ERROR_CODE);
    }
}
