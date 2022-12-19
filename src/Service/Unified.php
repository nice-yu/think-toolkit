<?php
declare(strict_types=1);

namespace NiceYu\Service;

use think\App;

class Unified
{
    /**
     * 获取统一请求配置
     * @param App $app
     * @return string|null
     */
    public static function getUnifiedRequestConfig(App $app): ?string
    {
        $unified = $app->request->pathinfo();

        if ($app->config->get('toolkit.unified.enable',true)){
            $param  = $app->config->get('toolkit.unified.param',true);
            $param  = ($param == true)? 'header': 'param';
            $unified= $app->config->get('toolkit.unified.name','method');
            $unified= $app->request->{$param}($unified);

            /** 替换掉 分割符 */
            $unified= str_replace($app->config->get('toolkit.unified.symbol','.'),'/',$unified);
        }
        return $unified;
    }
}