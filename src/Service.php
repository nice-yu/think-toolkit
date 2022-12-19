<?php
declare(strict_types=1);
namespace NiceYu;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use ReflectionException;
use think\exception\HttpException;

class Service extends \think\Service
{
    use Toolkit;

    /** @var Reader */
    protected Reader $reader;


    public function register()
    {
        /** 绑定注解类进 app */
        $this->app->bind(Reader::class, new AnnotationReader());
    }

    public function boot(Reader $reader)
    {
        $this->reader = $reader;

        /** 注解路由 */
        try {
            $this->registerToolkit();
        } catch (ReflectionException $e) {
            throw new HttpException(500,'Toolkit 出现错误: ' . $e->getMessage());
        }
    }
}