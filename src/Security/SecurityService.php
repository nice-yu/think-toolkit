<?php
declare(strict_types=1);
namespace NiceYu\Security;

use NiceYu\Contract\UserProviderInterface;
use think\App;
use think\exception\HttpException;

class SecurityService
{

    /**
     * @param App $app
     * @param array $method
     * @return void
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

                /** dependency injection */
                $provider = explode('\\', $module[$currentModule]);
                $providerName = array_pop($provider);
                $app->bind($providerName, $module[$currentModule]);
                $userProvider = $app->get($providerName);

                /** 调动用户提供器 */
                if (!($userProvider instanceof UserProviderInterface)){
                    throw new HttpException(500,"请实现 UserProviderInterface 服务类");
                }

                if ($userProvider->supports($method['defaults'])){
                    /** 获取到凭证 */
                    $credentials = $userProvider->getCredentials();

                    /** 获取到用户信息 */
                    $app->request->userBadgeInfo  = $userProvider->getUser($credentials);
                    $app->request->userIdentifier = $credentials;
                }
            }
        }
    }
}