<?php
declare(strict_types=1);

namespace NiceYu\Toolkit\Annotation;

use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

/**
 * Annotation class for @Validator().
 * @Annotation
 * @NamedArgumentConstructor
 * @Target("PROPERTY")
 */
class Validator
{
    /**
     * validation rules
     * @var ?array
     */
    private ?array $rule;

    /**
     * Verification scenario
     * @var ?array
     */
    private ?array $scene;

    /**
     * Verification error output prompt
     * @var ?array
     */
    private ?array $message;

    /**
     * Validator constructor.
     * @param array|null $rule
     * @param array|null $scene
     * @param array|null $message
     */
    public function __construct(?array $rule, ?array $scene, ?array $message)
    {
        $this->rule = $rule;
        $this->scene = $scene;
        $this->message = $message;
    }

    /**
     * @return array|null
     */
    public function getRule(): ?array
    {
        return $this->rule;
    }

    /**
     * @return array|null
     */
    public function getScene(): ?array
    {
        return $this->scene;
    }

    /**
     * @return array|null
     */
    public function getMessage(): ?array
    {
        return $this->message;
    }
}