<?php
declare(strict_types=1);
namespace NiceYu\Cache;

use JMS\Serializer\Annotation as Serializer;
use NiceYu\Contract\CacheDtoInterface;

abstract class AbstractCacheDto implements CacheDtoInterface
{
    /**
     * @Serializer\Exclude
     */
    protected string $cacheKey;

    public function getCacheKey(): string
    {
        return $this->cacheKey;
    }

    public function setCacheKey(...$arg)
    {
        $this->cacheKey = sprintf($this->cacheKey, ...$arg);
    }

}