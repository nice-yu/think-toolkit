<?php
declare(strict_types=1);

namespace NiceYu\Toolkit\Contract;

interface CacheDtoInterface
{
    public function getCacheKey(): string;

    public function setCacheKey(...$arg);
}