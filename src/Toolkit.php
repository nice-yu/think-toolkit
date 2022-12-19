<?php
declare(strict_types=1);
namespace NiceYu;

use NiceYu\Annotation\Route;
use NiceYu\Service\Unified;
use NiceYu\Service\Version;
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
                $this->version = Version::getVersionConfig($this->app);

                /** 统一调度 */
                $this->unified = Unified::getUnifiedRequestConfig($this->app);

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
        [
            'controller'=> $controller,
            'cacheName' => $cacheName,
        ] = $this->app->config->get('toolkit.annotation', []);

        /** 获取路由缓存 */
        $cache = $this->app->cache->get($cacheName,null);

        /** 控制器文件夹名称 */
        $controllerName = $this->app->config->get('route.controller_layer');

        /** 扫描控制器 */
        foreach ($controller as $dirPath){
            $dirPath .= $controllerName;
            if (is_dir($dirPath)){
                $cache = $this->scanControllerDir($dirPath,$cache);
            }
        }
        /** 缓存当前路由信息 */
        $this->app->cache->set($cacheName, $cache);
    }

    /**
     * 扫描控制器目录
     * @param $dirPath
     * @param $cache
     * @return mixed
     * @throws ReflectionException
     */
    protected function scanControllerDir($dirPath,$cache)
    {
        $stopForeach = false;
        /** 扫描此目录下的所有控制器 */
        foreach (ClassMapGenerator::createMap($dirPath) as $class => $path){
            /** 查看文件是否有更改 */
            $fileMD5    =   md5($path);
            $contentMD5 =   md5_file($path);

            /** 获取到此控制器的方法 */
            if (!isset($cache[$fileMD5]['content_md5'])){
                $cache[$fileMD5] = $this->filePriority($class,$contentMD5);
            }

            /** 匹配缓存 */
            foreach ($cache[$fileMD5]['methods'] as $method){

                /** 相同的路由地址 */
                if ('/'.$this->unified === $method['route']){
                    /** 如果有代码版本要求 */
                    if (!empty($this->version) && in_array($this->version,$method['version'])){
                        $this->addRouteRule($method);
                        $stopForeach = true;
                        break;
                    }elseif (empty($this->version)){
                        /** 如果没有代码版本要求 */
                        $this->addRouteRule($method);
                        $stopForeach = true;
                        break;
                    }
                }
            }


            /** 停止扫描 */
            if ($stopForeach){
                break;
            }
        }
        return $cache;
    }

    /**
     * 添加路由规则
     * @param $method
     */
    protected function addRouteRule($method)
    {
        /** 加入当前请求地址和路由不一致, 直接变动注解 */
        if ($method['route'] !== $this->app->request->pathinfo()){
            $method['route'] = $this->app->request->pathinfo();
        }

        /** 写入路由 */
        $routeGroup = $this->route->getGroup();
        $rule = $routeGroup->addRule(
            $method['route'],$method['action'],strtoupper(implode("|",$method['method']))
        );
        $rule->option($method);

        $this->routeState= true;
    }

    /**
     * 遍历文件
     * @param string $class
     * @param string $contentMD5
     * @return array
     * @throws ReflectionException
     */
    protected function filePriority(string $class,string $contentMD5): array
    {
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

        /** 设置方法 */
        $methods = [];
        foreach ($refClass->getMethods(ReflectionMethod::IS_PUBLIC) as $refMethod) {

            /** @var Route $route */
            if ($route = $this->reader->getMethodAnnotation($refMethod, Route::class)) {

                /** 设置缓存 */
                $action = $refMethod->getName();
                $routePath = $this->getRequestAddress($route->getName(),$routeGroup);
                $methods[$action] = array(
                    'route'     =>  $routePath,
                    'action'    =>  "{$class}@{$refMethod->getName()}",
                    'method'    =>  empty($route->getMethod()) ? $groupMethod : $route->getMethod(),
                    'version'   =>  empty($route->getVersion()) ? $groupVersion : $route->getVersion(),
                    'defaults'  =>  empty($route->getDefaults()) ? $groupDefaults : $route->getDefaults(),
                );
            }
        }

        /** 设置缓存 */
        return array(
            'group'         =>  $routeGroup,
            'method'        =>  $groupMethod,
            'methods'       =>  $methods,
            'version'       =>  $groupVersion,
            'defaults'      =>  $groupDefaults,
            'namespace'     =>  $class,
            'content_md5'   =>  $contentMD5,
        );
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