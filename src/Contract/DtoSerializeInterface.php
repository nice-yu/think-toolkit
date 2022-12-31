<?php
declare(strict_types=1);

namespace NiceYu\Contract;

interface DtoSerializeInterface
{
    public function serialize(): string;
}