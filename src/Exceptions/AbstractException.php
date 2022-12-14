<?php
declare(strict_types=1);
namespace NiceYu\Exceptions;

use LogicException;

/**
 * Class AbstractException
 * @package NiceYu\exceptions
 */
abstract class AbstractException extends LogicException
{
    /**
     * @var int $code
     */
    public $code;

    /**
     * @var string $message
     */
    public $message;

    /** Parameter error */
    public const PARAMETER_ERROR = 1;

    /** unauthorized */
    public const UNAUTHORIZED_ERROR = 401;

    /** No permission */
    public const PAYMENT_ERROR = 403;

    /** does not exist */
    public const NOTFOUND_ERROR = 404;

    /** Server Error */
    public const SERVICE_ERROR = 500;
}