<?php
namespace app\exceptions;

use Exception;

class QueryExecuteFailedException extends Exception{
    public $errorMessage = "Exception - fail to execute query.";

    function __construct(string $message, string $class, string $method){
        $this->message = "{$this->errorMessage}  $message $class $method";
    }
}