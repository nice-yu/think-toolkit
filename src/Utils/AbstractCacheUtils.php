<?php
declare(strict_types=1);
namespace NiceYu\Toolkit\Utils;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use NiceYu\Toolkit\Contract\CacheDtoInterface;
use think\facade\Cache;

class AbstractCacheUtils
{
    private Serializer $serializer;

    public function __construct()
    {
        $this->serializer = SerializerBuilder::create()->build();
    }

    /**
     * get cache info
     * @param string $cacheName
     * @param mixed ...$keys
     * @return object|null
     */
    public function getObject(string $cacheName, ...$keys):?object
    {
        $object = new $cacheName(...$keys);
        if (!$object instanceof CacheDtoInterface) {
            return null;
        }
        $data = Cache::get($object->getCacheKey());
        if (!$data){
            return null;
        }
        $object = $this->serializer->deserialize($data,$cacheName,'json');
        $object->setCacheKey(...$keys);
        return $object;
    }

    /**
     * set cache info
     * @param CacheDtoInterface $cache
     * @param int $ttl
     * @return bool
     */
    public function setObject(CacheDtoInterface $cache, int $ttl = 0):bool
    {
        $data = $this->serializer->serialize($cache, 'json');
        return Cache::set($cache->getCacheKey(), $data, $ttl);
    }

    /**
     * remove cache info
     * @param CacheDtoInterface $cache
     * @return bool
     */
    public function delObject(CacheDtoInterface $cache):bool
    {
        return Cache::delete($cache->getCacheKey());
    }
}