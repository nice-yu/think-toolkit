<?php
declare(strict_types=1);
namespace NiceYu\Security;

use NiceYu\Contract\UserProviderInterface;
use ReflectionClass;
use ReflectionException;
use think\App;
use think\exception\HttpException;

class SecurityService
{

    /**
     * @param App $app
     * @param array $method
     * @return null
     */
    public function registerSecurity(App $app, array $method)
    {
        if ($app->config->get('toolkit.security.enable',true)){
            $name = $app->config->get('toolkit.security.name',true);

            /** 获取到携带参数 */
            if (isset($method['defaults'][$name])){
                $module = $app->config->get('toolkit.security.module',[]);

                /** 获取到安全模块 */
                $currentModule = $method['defaults'][$name];
                if (!isset($module[$currentModule])){
                    throw new HttpException(500,"尚未配置安全模块: $currentModule");
                }


                try {
                    /** 调动用户提供器 */
                    $refClass = (new ReflectionClass($module[$currentModule]))->newInstance();
                    if (!($refClass instanceof UserProviderInterface)){
                        throw new HttpException(500,"请实现 UserProviderInterface 服务类");
                    }
                } catch (ReflectionException $e) {
                    throw new HttpException(500,'错误的用户提供器: ' . $module[$currentModule]);
                }


                if ($refClass->supports($method['defaults'])){
                    /** 获取到凭证 */
                    $credentials = $refClass->getCredentials();

                    /** 获取到用户信息 */
                    $app->request->userBadgeInfo  = $refClass->getUser($credentials);
                    $app->request->userIdentifier = $credentials;
                }
                return null;
            }

        }
    }
}