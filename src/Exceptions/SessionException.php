<?php


namespace Neon\Cart\Exceptions;


use Exception;


class SessionException extends Exception
{
    /**
     * Exception message.
     *
     * @var string
     */
    protected $message = "No active PHP Session detected";
}