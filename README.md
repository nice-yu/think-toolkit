# think-toolkit

## 运行环境要求
> "php": ">=7.4", <br/>
> "topthink/framework": "^6.0", <br/>
> "doctrine/annotations": "^1.13", <br/>
> "symfony/class-loader": "~3.2.0", <br/>
> "jms/serializer": "^3.18" <br/>

## 主要特性
* 采用`PHP7`强类型（严格模式）
* 支持更多的`PSR`规范
* 面向对象的 `Cache`
* 兼容 `thinkPHP` 的 `Dto` 参数验证
* `Route` 更符合业务的注解路由
* 面向对象的 `Security` 安全防火墙
* 友好的多版本 `version` 注解
* 简单的注解 `unified` 统一路由

## 安装

~~~
composer require nice-yu/think-toolkit
~~~

## 文档
* [目录](https://github.com/nice-yu/think-toolkit/wiki)
* [Cache 缓存对象](https://github.com/nice-yu/think-toolkit/wiki/Cache-%E7%BC%93%E5%AD%98%E5%AF%B9%E8%B1%A1)
* [Dto 验证器](https://github.com/nice-yu/think-toolkit/wiki/Dto-%E9%AA%8C%E8%AF%81%E5%99%A8)
* [Route 注解路由](https://github.com/nice-yu/think-toolkit/wiki/Route-%E6%B3%A8%E8%A7%A3%E8%B7%AF%E7%94%B1)
* [Security 安全防火墙](https://github.com/nice-yu/think-toolkit/wiki/Security-%E5%AE%89%E5%85%A8%E9%98%B2%E7%81%AB%E5%A2%99)
* [Unified 统一路由](https://github.com/nice-yu/think-toolkit/wiki/Unified-%E7%BB%9F%E4%B8%80%E8%B7%AF%E7%94%B1)
* [Version 代码版本](https://github.com/nice-yu/think-toolkit/wiki/version-%E4%BB%A3%E7%A0%81%E7%89%88%E6%9C%AC)



## 例子
[toolkit-admin](https://github.com/nice-yu/toolkit-admin/releases/tag/v0.1) 
使用 `think-toolkit` 统一路由开发, 并且使用了双令牌的 `Security` 安全防火墙

## 参与开发

请参阅 [Think-Toolkit](https://github.com/nice-yu/think-toolkit)。
