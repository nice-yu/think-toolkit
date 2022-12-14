<?php
declare(strict_types=1);

namespace NiceYu\Exceptions;

class ServiceException extends AbstractException
{
    public $code = self::SERVICE_ERROR;

    public $message = 'Please wait';
}