<?php
declare(strict_types=1);
namespace NiceYu;

use NiceYu\Annotation\Route;
use NiceYu\Security\SecurityService;
use NiceYu\Service\UnifiedService;
use NiceYu\Service\VersionService;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Symfony\Component\ClassLoader\ClassMapGenerator;
use think\event\RouteLoaded;
use think\exception\HttpException;
use think\Route as ThinkRoute;

/**
 * Trait InteractsWithRoute
 * @package NiceYu
 */
trait Toolkit
{
    /**
     * @var ThinkRoute
     */
    protected ThinkRoute $route;

    protected bool $routeState = false;

    protected ?string $version;

    protected ?string $unified;

    /**
     * 注册工具类
     * @throws ReflectionException
     */
    protected function registerToolkit()
    {
        /** 开启路由注解 */
        if ($this->app->config->get('toolkit.annotation.enable', true)){

            /** 使用注解路由 */
            $this->app->event->listen(RouteLoaded::class, function (){

                /** 获取当前路由 */
                $this->route = $this->app->route;

                /** 代码版本 */
                $this->version = VersionService::getVersionConfig($this->app);

                /** 统一调度 */
                $this->unified = UnifiedService::getUnifiedRequestConfig($this->app);

                /** 注解路由 */
                $this->getAnnotationRoute();

                if (!$this->routeState){
                    throw new HttpException(
                        500,'未找到匹配路由: ' . $this->unified .' 版本: ' .$this->version
                    );
                }
            });
        }
    }

    /**
     * 获取注解路由
     * @throws ReflectionException
     */
    protected function getAnnotationRoute()
    {
        /** 注解配置 */
        $controller = $this->app->config->get('toolkit.annotation.controller', []);

        /** 控制器文件夹名称 */
        $controllerName = $this->app->config->get('route.controller_layer');

        /** 扫描控制器 */
        foreach ($controller as $dirPath){
            $dirPath .= $controllerName;
            if (is_dir($dirPath)){
                $this->scanControllerDir($dirPath);
            }
        }
    }

    /**
     * 扫描控制器目录
     * @param $dirPath
     * @throws ReflectionException
     */
    protected function scanControllerDir($dirPath)
    {
        $stopForeach = false;
        /** 扫描此目录下的所有控制器 */
        foreach (ClassMapGenerator::createMap($dirPath) as $class => $path){
            $refClass   = new ReflectionClass($class);
            $routeGroup = false;
            $groupMethod = [];
            $groupVersion = [];
            $groupDefaults = [];

            /** @var Route $group */
            if ($group = $this->reader->getClassAnnotation($refClass, Route::class)) {
                $routeGroup     =   $group->getName();
                $groupMethod    =   $group->getMethod();
                $groupVersion   =   $group->getVersion();
                $groupDefaults  =   $group->getDefaults();
            }


            /** 获取到此控制器的方法 */
            foreach ($refClass->getMethods(ReflectionMethod::IS_PUBLIC) as $refMethod) {

                /** @var Route $route */
                if ($route = $this->reader->getMethodAnnotation($refMethod, Route::class)) {
                    /** 设置缓存 */
                    $routePath = $this->getRequestAddress($route->getName(),$routeGroup);

                    /** 相同的路由地址 */
                    if ('/'.$this->unified === $routePath){
                        $action = "{$class}@{$refMethod->getName()}";
                        $method = empty($route->getMethod()) ? $groupMethod : $route->getMethod();
                        $version = empty($route->getVersion()) ? $groupVersion : $route->getVersion();
                        $defaults = empty($route->getDefaults()) ? $groupDefaults : $route->getDefaults();

                        /** 如果有代码版本要求 */
                        if (!empty($this->version) && in_array($this->version,$version)){
                            $this->addRouteRule($action,$routePath,$method,$version,$defaults);
                            $stopForeach = true;
                            break;
                        }elseif (empty($this->version)){
                            /** 如果没有代码版本要求 */
                            $this->addRouteRule($action,$routePath,$method,$version,$defaults);
                            $stopForeach = true;
                            break;
                        }
                    }
                }

            }



            /** 停止扫描 */
            if ($stopForeach){
                break;
            }
        }
    }

    /**
     * 添加路由规则
     * @param string $action
     * @param string $routePath
     * @param array $method
     * @param array $version
     * @param array $defaults
     */
    protected function addRouteRule(string $action,string $routePath,array $method,array $version,array $defaults)
    {
        /** 加入当前请求地址和路由不一致, 直接变动注解 */
        $currentPath = $routePath;
        if ($routePath !== $this->app->request->pathinfo()){
            $routePath = $this->app->request->pathinfo();
        }

        /** 写入路由 */
        $routeGroup = $this->route->getGroup();
        $rule = $routeGroup->addRule(
            $routePath,$action,strtoupper(implode("|",$method))
        );
        $rule->option([
            'action'    =>  $action,
            'method'    =>  $method,
            'version'   =>  $version,
            'defaults'  =>  $defaults,
            'routePath' =>  $currentPath,
        ]);

        $this->routeState= true;

        /** 执行安全 */
        $security = new SecurityService();
        $security->registerSecurity($this->app, $method);
    }

    /**
     * 获取请求地址
     * @param string $routePath
     * @param $routeGroupPath
     * @return string
     */
    protected function getRequestAddress(string $routePath,$routeGroupPath):string
    {
        /** 方法自带 '/' */
        if (0 === strpos($routePath,'/')){
            return $routePath;
        }

        /** 类为 false */
        if (false === $routeGroupPath){
            return "/$routePath";
        }

        /** 类存在 '/' */
        if (0 === strpos($routeGroupPath,'/')){
            return "$routeGroupPath/$routePath";
        }else{
            return "/$routeGroupPath/$routePath";
        }
    }
}