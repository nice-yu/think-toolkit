<?php
declare(strict_types=1);

namespace NiceYu\Annotation;

use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

/**
 * Annotation class for @ValidatorGroup().
 * @Annotation
 * @NamedArgumentConstructor
 * @Target("METHOD")
 */
class ValidatorGroup
{
    /**
     * Verification scenario
     * @var string
     */
    private string $name;

    /**
     * ValidatorGroup constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}