<?php

namespace Kfirba\Directo\Exceptions;

use InvalidArgumentException;

class InvalidExpiresStringException extends InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct('Expiry must be between 1 second and 7 days in the future.');
    }
}
