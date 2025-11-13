<?php

namespace extend\Utils;

use InvalidArgumentException;
use support\exception\BusinessException;
use Throwable;
use Workerman\Timer;
use Workerman\Worker;

class Util
{
    /**
     * 密码哈希
     *
     * @param string $password
     * @param string $algo
     *
     * @return string
     */
    public static function passwordHash(string $password, string $algo = PASSWORD_DEFAULT): string
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


    /**
     * 隐藏字符串中一部分
     *
     * @param string $str
     * @param int    $start
     * @param int    $length
     *
     * @return string
     */
    public static function hideStr(string $str, int $start = 3, int $length = 4): string
    {
        return substr_replace($str, '****', $start, $length);
    }

    /**
     * 两个任意精度的数字计算
     *
     * @param float  $n1     计算数字1
     * @param string $symbol 计算方式
     * @param float  $n2     计算数字2
     * @param int    $scale  精度
     *
     * @return string|int
     */
    public static function calculate(float $n1, string $symbol, float $n2, int $scale = 2): string|int
    {
        switch ($symbol) {
            case "+": // 加法
                $res = bcadd($n1, $n2, $scale);
                break;
            case "-": // 减法
                $res = bcsub($n1, $n2, $scale);
                break;
            case "*": // 乘法
                $res = bcmul($n1, $n2, $scale);
                break;
            case "/": // 除法
                if (bccomp($n2, '0', $scale) === 0) {
                    throw new InvalidArgumentException("Division by zero");
                }
                $res = bcdiv($n1, $n2, $scale);
                break;
            case "%": // 取模
                $res = bcmod($n1, $n2);
                break;
            default: // 比较大小 > = <
                $res = bccomp($n1, $n2, $scale);
                break;
        }
        return $res;
    }

    /**
     * 金额格式化函数
     *
     * @param float|string|int|null $value        待格式化金额
     * @param int                   $decimals     小数位数，默认 2
     * @param string                $decimalSep   小数点分隔符，默认 "."
     * @param string                $thousandsSep 千分位分隔符，默认 ","
     *
     * @return string
     */
    public static function priceFormat(float|string|int|null $value, int $decimals = 2, string $decimalSep = '.', string $thousandsSep = ','): string
    {
        if ($value === null || $value === '') {
            $value = 0;
        }

        // 强制转换为浮点数
        $value = (float)$value;

        return number_format($value, $decimals, $decimalSep, $thousandsSep);
    }

    /**
     * 获取字符串长度（支持多语言、多字节字符）
     *
     * @param string $str      输入字符串
     * @param int    $multiLen 多字节字符长度，默认 2
     * @param string $encoding 字符编码，默认 UTF-8
     *
     * @return int
     */
    public static function getStrLen(string $str, int $multiLen = 2, string $encoding = 'UTF-8'): int
    {
        if ($multiLen <= 1) {
            // 简单返回字符数
            return mb_strlen($str, $encoding);
        }

        $length = 0;
        $chars = preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($chars as $char) {
            // 单字节字符（ASCII）按 1 计，多字节字符按 $multiLen
            $length += (strlen($char) === 1) ? 1 : $multiLen;
        }

        return $length;
    }


}