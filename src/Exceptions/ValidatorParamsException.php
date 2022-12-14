<?php
declare(strict_types=1);

namespace NiceYu\Exceptions;

class ValidatorParamsException extends AbstractException
{
    public $code = self::PARAMETER_ERROR;

    public $message = 'Parameter error';
}