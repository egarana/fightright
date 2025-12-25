<?php

namespace App\Exceptions;

use Exception;

class AlreadyCheckedInException extends Exception
{
    public function __construct(string $message = 'Member is already checked-in.')
    {
        parent::__construct($message);
    }
}
