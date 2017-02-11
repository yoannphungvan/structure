<?php
/**
 * Missing Parameter exception
 *
 * Copyright 2014 - SSENSE
 */

namespace PROJECT\Exceptions;

class MethodNotAllowedException extends BaseException
{
    const ERROR_CODE = 405;

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
