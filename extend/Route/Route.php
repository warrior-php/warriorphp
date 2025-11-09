<?php

namespace extend\Route;

use Attribute;

/**
 * 控制器方法路由定义
 * 用法：
 * #[RouteAttr(path: "/user/login", methods: ["POST"], permission: "user.login")]
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Route
{
    /**
     * @var string
     */
    public string $path;

    /**
     * @var array|string[]
     */
    public array $methods;

    /**
     * @var string|null
     */
    public ?string $permission;

    /**
     * @param string      $path       路径
     * @param array       $methods    请求方法
     * @param string|null $permission 权限
     */
    public function __construct(string $path, array $methods = ['ANY'], ?string $permission = null)
    {
        $this->path = $path;
        $this->methods = $methods;
        $this->permission = $permission;
    }
}