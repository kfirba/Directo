<?php
namespace Kfirba\Directo\Exceptions;

use Kfirba\Directo\Options;
use InvalidArgumentException;

class InvalidOptionsException extends InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct(sprintf(
            "The Options must to be either an instance of [%s] or an array",
            Options::class
        ));
    }
}