<?php

namespace App;

use App\Attributes\Route as RouteAttr;
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
        // 需要扫描的根目录及命名空间前缀
        $scanDirs = [
            base_path('addons') => 'addons'
        ];

        foreach ($scanDirs as $rootDir => $namespacePrefix) {
            if (!is_dir($rootDir)) continue;
            foreach (scandir($rootDir) as $appDir) {
                if ($appDir === '.' || $appDir === '..') continue;
                $controllerDir = "$rootDir/$appDir/controller";
                if (!is_dir($controllerDir)) continue;
                $namespace = "$namespacePrefix\\$appDir\\controller";
                self::scanAndRegisterRecursive($controllerDir, $namespace);
            }
        }

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
            if ($item === '.' || $item === '..') continue;

            $path = "$dir/$item";

            // 如果是目录，递归扫描
            if (is_dir($path)) {
                $subNamespace = $namespace . '\\' . $item;
                self::scanAndRegisterRecursive($path, $subNamespace);
            }

            // 如果是 PHP 文件，注册路由
            if (is_file($path) && str_ends_with($item, '.php')) {
                $class = $namespace . '\\' . basename($item, '.php');
                if (!class_exists($class)) continue;

                $ref = new ReflectionClass($class);
                foreach ($ref->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                    foreach ($method->getAttributes(RouteAttr::class) as $attr) {
                        /** @var RouteAttr $meta */
                        $meta = $attr->newInstance();
                        $callback = [$class, $method->getName()];
                        // 处理 name: 去掉开头的 /，其余 / 替换为 .
                        $routeName = ltrim($meta->path, '/'); // 去掉开头 /
                        $routeName = str_replace('/', '.', $routeName); // / -> .
                        foreach ($meta->methods as $httpMethod) {
                            $httpMethod = strtoupper($httpMethod);
                            if ($httpMethod === 'ANY') {
                                Route::any($meta->path, $callback)->name($routeName);
                            } else {
                                Route::add($httpMethod, $meta->path, $callback)->name($routeName);
                            }
                        }
                    }
                }
            }
        }
    }
}