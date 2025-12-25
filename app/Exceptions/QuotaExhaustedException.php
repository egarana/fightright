<?php

namespace App\Exceptions;

use Exception;

class QuotaExhaustedException extends Exception
{
    public function __construct(string $message = 'Attendance quota has been exhausted.')
    {
        parent::__construct($message);
    }
}
