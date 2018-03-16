<?php

namespace Kfirba\Directo\Exceptions;

use InvalidArgumentException;

class InvalidRegionException extends InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct('The AWS region specified is not valid. Try checking: http://amzn.to/1FtPG6r');
    }
}
