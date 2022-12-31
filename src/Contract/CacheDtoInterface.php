<?php
declare(strict_types=1);

namespace NiceYu\Contract;

interface CacheDtoInterface
{
    public function getCacheKey(): string;

    public function setCacheKey(...$arg);
}