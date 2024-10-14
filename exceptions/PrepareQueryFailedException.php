<?php
namespace app\exceptions;

use Exception;

class PrepareQueryFailedException extends Exception
{
    public $errorMessage = "Exception - fail to prepare query.";

    function __construct(string $message, string $class, string $method)
    {
        $this->message = "{$this->errorMessage} $message $class $method";
    }
}