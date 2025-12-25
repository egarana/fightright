<?php

namespace App\Exceptions;

use Exception;

class NotCheckedInException extends Exception
{
    public function __construct(string $message = 'Member is not currently checked-in.')
    {
        parent::__construct($message);
    }
}
