<?php
declare(strict_types=1);
namespace NiceYu\Annotation;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

/**
 * Annotation class for @Route().
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"METHOD","CLASS"})
 */
final class Route
{
    /**
     * 请求地址
     * @var string
     */
    private string $name;

    /**
     * 请求类型
     * @var array
     */
    private array $method;

    /**
     * 请求版本
     * @var array
     */
    private array $version;

    /**
     * 默认参数
     * @var array
     */
    private array $defaults;

    /**
     * Route constructor.
     * @param string $name
     * @param array $method
     * @param array $version
     * @param array $defaults
     */
    public function __construct(
        string $name,
        array $method = [],
        array $version = [],
        array $defaults = []
    )
    {
        $this->name = $name;
        $this->method = $method;
        $this->version = $version;
        $this->defaults = $defaults;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getMethod(): array
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getVersion(): array
    {
        return $this->version;
    }

    /**
     * @return array
     */
    public function getDefaults(): array
    {
        return $this->defaults;
    }
}