<?php
declare(strict_types=1);
namespace NiceYu\Toolkit\Dto;

use stdClass;
use think\Response;

abstract class AbstractResponseDtoTransformer extends AbstractDtoTransformer
{
    public function transformerResponse($code = 0, $msg = 'ok'): Response
    {
        $a = new stdClass();
        $a->code = $code;
        $a->message = $msg;
        $a->result = $this;

        return Response::create($a, 'json');
    }
}