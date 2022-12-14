<?php
declare(strict_types=1);

namespace NiceYu\Exceptions;

class NotFoundException extends AbstractException
{
    public $code = self::NOTFOUND_ERROR;

    public $message = 'Data is empty';
}