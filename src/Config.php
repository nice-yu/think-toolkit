<?php
declare(strict_types=1);

return array(
    /**
     * 注解开启
     */
    'annotation'=>[
        'enable'    =>  true,       // 使用注解路由 (true | false)
        'controller'=>  [
            think\facade\App::getAppPath(), // 单应用
//            think\facade\App::getAppPath() . 'admin/',  //多应用
        ],
        'cacheName' =>  'classMD5', // 缓存名称
    ],

    /**
     * 代码版本
     * 开启后为必须 (==) 不会出现 1.2 不存在时, 调用 1.1
     */
    'version'   =>  [
        'enable'    =>  true,       // 使用代码版本 (true | false)
        'param'     =>  false,      // 版本参数位置 (true = header | false = body)
        'name'      =>  'version',  // 中转版本参数 (1.0 | 2.0 | 3.0)
    ],

    /**
     * 开启的情况下, 所有请求都会被内部修改
     * 同时按照 `method` 参数进行调度
     */
    'unified'   =>  [
        'enable'    =>  true,       // 统一请求 (true | false)
        'param'     =>  false,      // 请求路由参数位置 (true = header | false = body)
        'name'      =>  'method',   // 请求路由 (/version | /hello)

        // (/api/welcome = api.welcome) (/admin/test = admin.test)
        'symbol'    =>  '.',        // 分割符号 (. | _ | - | @)
    ],

    /**
     * 安全守卫
     * 此验证会在中间件前调用
     */
    'security'  =>  [
        'enable'    =>  true,       // 安全守卫 (true | false)
        'name'      =>  'security', // 绑定安全参数名称

        // 模块 ( 模块 => 用户提供者 )
        'module'    =>  [
        ]
    ],
);