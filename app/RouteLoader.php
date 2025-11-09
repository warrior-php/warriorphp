<?php
declare(strict_types=1);

namespace App;

use App\Middleware\AccessControl;
use App\Route as RouteAttr;
use ReflectionClass;
use ReflectionMethod;
use Webman\Route;

class RouteLoader
{
    /**
     * 已注册的路由缓存（避免重复注册）
     * @var array
     */
    protected static array $registeredRoutes = [];

    /**
     * 注册应用路由
     *
     * 扫描 addons、app/Controller 目录，
     * 并注册控制器方法上标记的 RouteAttr 注解路由。
     *
     * @return void
     */
    public static function registerRoutes(): void
    {
        $appControllerDir = base_path('app/Controller');

        if (is_dir($appControllerDir)) {
            self::scanAndRegisterRecursive($appControllerDir, 'App\Controller');
        }
    }

    /**
     * 判断路由是否已注册
     *
     * @param string $method
     * @param string $path
     *
     * @return bool
     */
    protected static function isRegistered(string $method, string $path): bool
    {
        $key = strtoupper($method) . ':' . $path;
        if (isset(self::$registeredRoutes[$key])) {
            return true;
        }
        self::$registeredRoutes[$key] = true;
        return false;
    }

    /**
     * 递归扫描目录下的控制器文件并注册路由
     *
     * @param string $dir       控制器目录
     * @param string $namespace 控制器命名空间
     *
     * @return void
     */
    protected static function scanAndRegisterRecursive(string $dir, string $namespace): void
    {
        foreach (scandir($dir) as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = "$dir/$item";
            // 递归扫描子目录
            if (is_dir($path)) {
                $subNamespace = $namespace . '\\' . $item;
                self::scanAndRegisterRecursive($path, $subNamespace);
                continue;
            }
            // 处理 PHP 控制器文件
            if (is_file($path) && str_ends_with($item, '.php')) {
                $class = $namespace . '\\' . basename($item, '.php');
                if (!class_exists($class)) {
                    continue;
                }
                $ref = new ReflectionClass($class);
                foreach ($ref->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                    foreach ($method->getAttributes(RouteAttr::class) as $attr) {
                        $meta = $attr->newInstance();
                        $callback = [$class, $method->getName()];
                        // 生成路由名：去掉首个 /，其余 / 转为 .
                        $routeName = ltrim($meta->path, '/');
                        $routeName = str_replace('/', '.', $routeName);
                        foreach ($meta->methods as $httpMethod) {
                            $httpMethod = strtoupper($httpMethod);
                            // 检查是否已经注册
                            if (self::isRegistered($httpMethod, $meta->path)) {
                                continue;
                            }
                            // 注册路由
                            $route = $httpMethod === 'ANY' ? Route::any($meta->path, $callback) : Route::add($httpMethod, $meta->path, $callback);
                            $route->name($routeName)->middleware([AccessControl::class]);
                        }
                    }
                }
            }
        }
    }

}
