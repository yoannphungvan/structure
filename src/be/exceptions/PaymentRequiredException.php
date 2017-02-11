<?php
/**
 *  Payment Exception
 *
 *  Copyright 2014 - SSENSE
 */

namespace PROJECT\Exceptions;

class PaymentRequiredException extends BaseException
{
    const ERROR_CODE = 402;

    /**
     * Constructor
     *
     * @param string $message The msg key for translation or straight up message.
     */
    public function __construct($message = '')
    {
        parent::__construct($message, self::ERROR_CODE);
    }
}
