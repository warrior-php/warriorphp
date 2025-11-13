<?php

namespace extend\Utils;

use support\exception\BusinessException;
use Throwable;
use Workerman\Timer;
use Workerman\Worker;

class Util
{
    /**
     * 密码哈希
     *
     * @param        $password
     * @param string $algo
     *
     * @return string
     */
    public static function passwordHash($password, string $algo = PASSWORD_DEFAULT): string
    {
        return password_hash($password, $algo);
    }

    /**
     * 验证密码哈希
     *
     * @param string $password
     * @param string $hash
     *
     * @return bool
     */
    public static function passwordVerify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * 格式化文件大小
     *
     * @param $file_size
     *
     * @return string
     */
    public static function formatBytes($file_size): string
    {
        $size = sprintf("%u", $file_size);
        if ($size == 0) {
            return ("0 Bytes");
        }
        $size_name = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
        return round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $size_name[$i];
    }

    /**
     * 检查表名是否合法
     *
     * @param string $table
     *
     * @return string
     * @throws BusinessException
     */
    public static function checkTableName(string $table): string
    {
        if (!preg_match('/^[a-zA-Z_0-9]+$/', $table)) {
            throw new BusinessException('表名不合法');
        }
        return $table;
    }

    /**
     * 变量或数组中的元素只能是字母数字下划线组合
     *
     * @param $var
     *
     * @return mixed
     */
    public static function filterAlphaNum($var): mixed
    {
        $vars = (array)$var;
        array_walk_recursive($vars, function ($item) {
            if (is_string($item) && !preg_match('/^[a-zA-Z_0-9]+$/', $item)) {
                throw new BusinessException('参数不合法');
            }
        });
        return $var;
    }

    /**
     * 变量或数组中的元素只能是字母数字
     *
     * @param $var
     *
     * @return mixed
     * @throws BusinessException
     */
    public static function filterNum($var): mixed
    {
        $vars = (array)$var;
        array_walk_recursive($vars, function ($item) {
            if (is_string($item) && !preg_match('/^[0-9]+$/', $item)) {
                throw new BusinessException('参数不合法');
            }
        });
        return $var;
    }

    /**
     * 检测是否是合法Path
     *
     * @param $var
     *
     * @return string
     * @throws BusinessException
     */
    public static function filterPath($var): string
    {
        if (!is_string($var) || !preg_match('/^[a-zA-Z0-9_\-\/]+$/', $var)) {
            throw new BusinessException('参数不合法');
        }
        return $var;
    }

    /**
     * 类转换为url path
     *
     * @param $controller_class
     *
     * @return false|string
     */
    static function controllerToUrlPath($controller_class): false|string
    {
        $key = strtolower($controller_class);
        $action = '';
        if (strpos($key, '@')) {
            [$key, $action] = explode('@', $key, 2);
        }
        $prefix = 'plugin';
        $paths = explode('\\', $key);
        if (count($paths) < 2) {
            return false;
        }
        $base = '';
        if (str_starts_with($key, "$prefix\\")) {
            if (count($paths) < 4) {
                return false;
            }
            array_shift($paths);
            $plugin = array_shift($paths);
            $base = "/app/$plugin/";
        }
        array_shift($paths);
        foreach ($paths as $index => $path) {
            if ($path === 'controller') {
                unset($paths[$index]);
            }
        }
        $suffix = 'controller';
        $code = $base . implode('/', $paths);
        if (str_ends_with($code, $suffix)) {
            $code = substr($code, 0, -strlen($suffix));
        }
        return $action ? "$code/$action" : $code;
    }

    /**
     * 转换为驼峰
     *
     * @param string $value
     *
     * @return string
     */
    public static function camel(string $value): string
    {
        static $cache = [];
        $key = $value;

        if (isset($cache[$key])) {
            return $cache[$key];
        }

        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return $cache[$key] = str_replace(' ', '', $value);
    }

    /**
     * 转换为小驼峰
     *
     * @param $value
     *
     * @return string
     */
    public static function smCamel($value): string
    {
        return lcfirst(static::camel($value));
    }

    /**
     * 获取注释中第一行
     *
     * @param $comment
     *
     * @return false|mixed|string
     */
    public static function getCommentFirstLine($comment): mixed
    {
        if ($comment === false) {
            return false;
        }
        foreach (explode("\n", $comment) as $str) {
            if ($s = trim($str, "*/\ \t\n\r\0\x0B")) {
                return $s;
            }
        }
        return $comment;
    }

    /**
     * 获取某个composer包的版本
     *
     * @param string $package
     *
     * @return string
     */
    public static function getPackageVersion(string $package): string
    {
        $installed_php = base_path('vendor/composer/installed.php');
        if (is_file($installed_php)) {
            $packages = include $installed_php;
        }
        return substr($packages['versions'][$package]['version'] ?? 'unknown  ', 0, -2);
    }

    /**
     * Reload warrior
     * @return bool
     */
    public static function reloadWarrior(): bool
    {
        if (function_exists('posix_kill')) {
            try {
                posix_kill(posix_getppid(), SIGUSR1);
                return true;
            } catch (Throwable $e) {
            }
        } else {
            Timer::add(1, function () {
                Worker::stopAll();
            });
        }
        return false;
    }
}