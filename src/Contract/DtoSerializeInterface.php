<?php
declare(strict_types=1);

namespace NiceYu\Toolkit\Contract;

interface DtoSerializeInterface
{
    public function serialize(): string;
}