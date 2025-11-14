<?php
declare(strict_types=1);

namespace App;

use App\Route as RouteAttr;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Throwable;
use Webman\Route;

class RouteLoader
{
    /**
     * 路由注册缓存（防止重复注册）
     * @var array
     */
    protected static array $registered = [];

    /**
     * 注册所有带注解的控制器路由
     *
     * @param string $controllerDir 控制器根目录
     * @param string $namespace     控制器命名空间
     */
    public static function register(string $controllerDir = '', string $namespace = 'App\\Controller'): void
    {
        $controllerDir = $controllerDir ?: base_path('app/Controller');

        if (!is_dir($controllerDir)) {
            return;
        }

        $files = self::getPhpFiles($controllerDir);
        foreach ($files as $file) {
            $class = $namespace . '\\' . str_replace('/', '\\', substr($file, strlen($controllerDir) + 1, -4));

            if (!class_exists($class)) {
                continue;
            }

            try {
                self::registerClassRoutes($class);
            } catch (Throwable $e) {
                echo "[RouteLoader] Failed to register $class: {$e->getMessage()}\n";
            }
        }
    }

    /**
     * 获取目录下所有 PHP 文件（递归）
     */
    protected static function getPhpFiles(string $dir): array
    {
        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
        $files = [];
        foreach ($rii as $file) {
            if ($file->isFile() && str_ends_with($file->getFilename(), '.php')) {
                $files[] = str_replace('\\', '/', $file->getPathname());
            }
        }

        return $files;
    }


    /**
     * 为单个控制器类注册带 #[Route] 的方法
     *
     * @param string $class
     *
     * @return void
     * @throws ReflectionException
     */
    protected static function registerClassRoutes(string $class): void
    {
        $ref = new ReflectionClass($class);

        foreach ($ref->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            foreach ($method->getAttributes(RouteAttr::class) as $attr) {
                $meta = $attr->newInstance();

                $path = self::normalizePath($meta->path);
                $methods = $meta->methods ?: ['GET'];
                $callback = [$class, $method->getName()];

                // 自动生成路由名：/admin/account/login → admin.account.login
                $routeName = str_replace('/', '.', ltrim($path, '/'));

                foreach ($methods as $httpMethod) {
                    $httpMethod = strtoupper($httpMethod);

                    if (self::isRegistered($httpMethod, $path)) {
                        continue;
                    }

                    // 注册路由
                    $route = $httpMethod === 'ANY' ? Route::any($path, $callback) : Route::add($httpMethod, $path, $callback);

                    $route->name($routeName);

                    // 添加中间件（如果有）
                    if (!empty($meta->middleware)) {
                        $route->middleware([self::resolveMiddleware($meta->middleware)]);
                    }

                    // 权限标识（如果定义了）
                    if (!empty($meta->permission) && method_exists($route, 'permission')) {
                        $route->permission($meta->permission);
                    }
                }
            }
        }
    }

    /**
     * 检查是否已注册（防止重复）
     */
    protected static function isRegistered(string $method, string $path): bool
    {
        $key = "$method:$path";
        if (isset(self::$registered[$key])) {
            return true;
        }
        self::$registered[$key] = true;
        return false;
    }

    /**
     * 解析中间件类名（支持简写）
     */
    protected static function resolveMiddleware(string $middleware): string
    {
        return class_exists($middleware) ? $middleware : "\\App\\BaseMiddleware\\$middleware";
    }

    /**
     * 格式化路径（去掉多余斜杠）
     */
    protected static function normalizePath(string $path): string
    {
        $path = '/' . trim($path, '/');
        return preg_replace('#/+#', '/', $path);
    }
}