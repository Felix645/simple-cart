<?php


namespace Neon\Cart\Exceptions;


use Exception;
use Throwable;


class BadMethodCallException extends Exception
{
    /**
     * BadMethodCallException constructor.
     *
     * @param string $class
     * @param string $method
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $class, string $method, $code = 0, Throwable $previous = null)
    {
        $message = "Method $class::$method not found";
        parent::__construct($message, $code, $previous);
    }
}