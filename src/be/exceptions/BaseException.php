<?php
/**
 *  Base Exception
 *
 *  All exception here should here of this one
 *
 * Copyright 2014 - SSENSE
 */

namespace PROJECT\Exceptions;

class BaseException extends \Exception
{
    /**
     * Constructor
     *
     * @param string $message The msg key for translation or straight up message.
     */
    public function __construct($message = '', $code)
    {
        parent::__construct($message, $code);
    }
}
