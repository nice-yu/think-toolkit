<?php
declare(strict_types=1);

namespace NiceYu\Exceptions;

class NoPermissionException extends AbstractException
{
    public $code = self::PAYMENT_ERROR;

    public $message = 'No permission';
}