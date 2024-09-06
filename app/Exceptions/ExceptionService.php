<?php

namespace App\Exceptions;

use Exception;

class ExceptionService extends Exception
{
    public function __construct($message = "Erreur dans le service", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
