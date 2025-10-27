<?php

namespace App;

use App\Middleware\AccessControl;
use extend\Attribute\Route as RouteAttr;
use ReflectionClass;
use ReflectionMethod;
use Webman\Route;

class InitRoute
{
    /**
     * 注册应用路由
     *
     * 扫描 addons、app/Controller 目录，
     * 并注册控制器方法上标记的 Route 注解路由。
     *
     * @return void
     */
    public static function registerRoutes(): void
    {
        // TODO 这里需要处理多应用的路由配置
        // 扫描 app/Controller
        $appControllerDir = base_path('app/Controller');
        if (is_dir($appControllerDir)) {
            self::scanAndRegisterRecursive($appControllerDir, 'App\Controller');
        }
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
                            $route = $httpMethod === 'ANY' ? Route::any($meta->path, $callback) : Route::add($httpMethod, $meta->path, $callback);
                            $route->name($routeName)->middleware([
                                AccessControl::class,
                            ]);
                        }
                    }
                }
            }
        }
    }
}