<?php

namespace Kfirba\Directo\Exceptions;

use InvalidArgumentException;

class InvalidACLException extends InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct('The ACL specified is not valid. Try checking: http://amzn.to/1SSOgwO');
    }
}
