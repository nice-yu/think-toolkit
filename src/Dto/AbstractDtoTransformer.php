<?php
declare(strict_types=1);
namespace NiceYu\Dto;

use Closure;
use JMS\Serializer\SerializerBuilder;
use NiceYu\Contract\DtoSerializeInterface;

abstract class AbstractDtoTransformer implements DtoSerializeInterface
{
    public function serialize(): string
    {
        $serialize = SerializerBuilder::create()->build();
        return $serialize->serialize($this, 'json');
    }

    public function transArrayObjects(iterable $objects, Closure $closure):iterable
    {
        $dto = [];

        foreach ($objects as $object) {
            $resp = $closure($object);
            if (!is_null($resp)){
                if (is_array($resp)){
                    list($keys,$data) = $resp;
                    $dto[$keys] = $data;
                }else{
                    $dto[] = $resp;
                }
            }
        }

        return $dto;
    }
}