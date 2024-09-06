<?php

namespace App\Exceptions;

use Exception;

class ExceptionRepository extends Exception
{
    public function __construct($message = "Erreur dans le repository", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
