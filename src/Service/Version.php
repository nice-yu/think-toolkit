<?php
declare(strict_types=1);
namespace NiceYu\Service;

use think\App;

class Version
{
    /**
     * 获取代码版本配置
     * @param App $app
     * @return string|null
     */
    public static function getVersionConfig(App $app): ?string
    {
        $version = null;
        if ($app->config->get('toolkit.version.enable',true)){
            $param  = $app->config->get('toolkit.version.param',true);
            $param  = ($param == true)? 'header': 'param';
            $version= $app->config->get('toolkit.version.name','version');
            $version= $app->request->{$param}($version);
        }
        return $version;
    }
}