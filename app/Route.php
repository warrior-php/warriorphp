<?php
declare(strict_types=1);

namespace App;

use Attribute;

/**
 * 控制器方法路由定义
 * 用法：
 * #[RouteAttr(path: "/user/login", methods: ["POST"], permission: "user.login")]
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::TARGET_FUNCTION)]
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
     * 权限码
     * @var string|null
     */
    public ?string $permission;

    /**
     * @var string|null
     */
    public ?string $middleware;

    /**
     * @param string      $path       路径
     * @param array       $methods    请求方法
     * @param string|null $middleware 权限
     */
    public function __construct(string $path, array $methods = ['ANY'], ?string $middleware = null, ?string $permission = null)
    {
        $this->path = $path;
        $this->methods = $methods;
        $this->middleware = $middleware;
        $this->permission = $permission;
    }
}