<?php

namespace App\Exceptions;

use RuntimeException;

class InsufficientStockException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('There is not enough stock available.');
    }
}
