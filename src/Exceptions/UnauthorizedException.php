<?php
declare(strict_types=1);

namespace NiceYu\Exceptions;

class UnauthorizedException extends AbstractException
{
    public $code = self::UNAUTHORIZED_ERROR;

    public $message = 'Unauthorized';
}