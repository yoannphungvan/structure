<?php
/**
 * API empty request exception
 *
 * Copyright 2014 - SSENSE
 */

namespace PROJECT\Exceptions;

class TooManyRequestsException extends BaseException
{
    const ERROR_CODE = 429;

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
