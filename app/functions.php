<?php
declare(strict_types=1);

use Godruoyi\Snowflake\Snowflake;
use Ramsey\Uuid\Uuid;
use support\Response;

/**
 * Here is functions.
 */
if (!function_exists('views_path')) {
    /**
     * views path
     *
     * @param string $path
     *
     * @return string
     */
    function views_path(string $path = 'views'): string
    {
        return path_combine(BASE_PATH . DIRECTORY_SEPARATOR . 'resource', $path);
    }
}

// 生成URL
if (!function_exists('url')) {
    /**
     * 生成URL
     *
     * @param string $name  路由名称
     * @param array  $param 路由参数
     *
     * @return string 生成的URL
     */
    function url(string $name = '', array $param = []): string
    {
        $route = route($name, $param);

        // Check if the route is '/'
        if ($route === '/') {
            return $route;
        }

        // Apply rtrim to remove trailing slashes
        return rtrim($route, '/');
    }
}

// 解析 Accept-Language HTTP 请求头来获取用户浏览器或设备的首选语言
if (!function_exists('setupLocale')) {
    /**
     * 初始化语言环境
     *
     * @param $request
     *
     * @return string
     * @throws Exception
     */
    function setupLocale($request): string
    {
        $supported = config('translation.fallback_locale', ['zh_CN', 'en', 'ja']);
        $default = config('translation.locale', 'en');
        $accept = strtolower($request->header('accept-language', ''));
        $lang = $default;

        if ($accept) {
            preg_match_all('/([a-z-]+)/i', $accept, $matches);
            foreach ($matches[1] as $raw) {
                $check = normalizeLang(str_replace('-', '_', $raw), $supported, $default);
                if ($check['supported']) {
                    $lang = $check['lang'];
                    break;
                }
            }
        }

        session()->set('lang', $lang);

        if (function_exists('locale_set')) {
            locale_set($lang);
        }

        return $lang;
    }
}


if (!function_exists('normalizeLang')) {
    /**
     * 标准化并验证语言
     *
     * @param string|null $lang
     * @param array|null  $supported
     * @param string|null $default
     *
     * @return array{lang: string, supported: bool}
     */
    function normalizeLang(?string $lang, ?array $supported = null, ?string $default = null): array
    {
        $supported = $supported ?? config('translation.fallback_locale', ['zh_CN', 'en', 'ja']);
        $default = $default ?? config('translation.locale', 'en');

        if (empty($lang)) {
            return ['lang' => $default, 'supported' => false];
        }

        // 规范化语言格式
        $normalized = str_replace('-', '_', strtolower($lang));

        if ($normalized === 'zh') $normalized = 'zh_cn';

        // 小写化支持列表并查找匹配
        foreach ($supported as $item) {
            if ($normalized === strtolower($item) || str_starts_with($normalized, strtolower($item))) {
                return ['lang' => $item, 'supported' => true];
            }
        }

        return ['lang' => $default, 'supported' => false];
    }
}


// Result
if (!function_exists('result')) {
    /**
     * @param int          $code
     * @param string|array $msg
     * @param array        $var
     *
     * @return Response
     */
    function result(int $code, string|array $msg = '', array $var = []): Response
    {
        $message = trans(STATUS_CODE[$code] ?? 'unknown');
        if (is_array($msg) || is_object($msg)) {
            $var = $msg;
            $data['msg'] = $message;
        } else {
            $data['msg'] = $msg ?: $message;
        }

        if (isset($var['url'])) {
            $data['url'] = $var['url'];
        }

        //控制返回的参数后台是否执行iframe父层
        if (isset($var['is_parent'])) {
            $data['is_parent'] = $var['is_parent'];
        }
        $data['code'] = $code;
        $data['data'] = $var;

        return new Response(200, ['Content-Type' => 'application/json'], json_encode($data, JSON_UNESCAPED_UNICODE));
    }
}

if (!function_exists('getAvailableTranslations')) {
    /**
     * 遍历 translations 目录，获取可用语言信息
     *
     * 每个子目录必须包含 messages.php 文件，
     * 且文件需返回 ['language' => '简体中文']。
     *
     * 返回结构:
     * [
     *     ['locale' => 'zh_CN', 'name' => '简体中文', 'current' => true],
     *     ['locale' => 'en', 'name' => 'English', 'current' => false],
     * ]
     *
     * @param string|null $path translations 目录路径
     *
     * @return array
     * @throws Exception
     */
    function getAvailableTranslations(?string $path = null): array
    {
        $path = $path ?? base_path('translations');
        $languages = [];

        if (!is_dir($path)) {
            return $languages;
        }

        $currentLang = strtolower(session('lang', '')); // 当前语言（统一小写）

        foreach (scandir($path) as $dir) {
            if ($dir === '.' || $dir === '..') {
                continue;
            }

            $localePath = $path . DIRECTORY_SEPARATOR . $dir;
            $messagesFile = $localePath . DIRECTORY_SEPARATOR . 'messages.php';

            if (is_dir($localePath) && is_file($messagesFile)) {
                try {
                    $messages = include $messagesFile;
                    if (is_array($messages) && isset($messages['key1'])) {
                        $languages[] = [
                            'locale'  => $dir,
                            'name'    => $messages['key1'],
                            'current' => strtolower($dir) === $currentLang,
                        ];
                    }
                } catch (Throwable $e) {
                    // 忽略单个文件错误，继续执行
                    continue;
                }
            }
        }

        return $languages;
    }
}

// 生成唯一标识符（UUID）
if (!function_exists('uuid')) {
    /**
     * 生成唯一标识符（UUID）。
     * 根据提供的算法类型生成不同格式的 UUID 或使用雪花算法生成唯一 ID。
     *
     * @param int        $type   算法类型
     *                           0：使用雪花算法生成唯一 ID（默认）。
     *                           1：基于时间的 UUID（UUID1）。
     *                           3：基于散列的 UUID（UUID3），使用 MD5 散列算法。
     *                           4：随机 UUID（UUID4）。
     *                           5：基于散列的 UUID（UUID5），使用 SHA1 散列算法。
     *                           6：基于主机 ID 和序列号的 UUID（UUID6）。
     *                           7：基于时间戳的 UUID（UUID7）。
     * @param bool       $number 是否返回数字形式的 UUID
     *                           true：返回整数形式的 UUID（适用于 UUID1、UUID3、UUID4、UUID5）。
     *                           false：返回字符串形式的 UUID（默认）。
     * @param int|string $data   用于生成 UUID 的附加数据（用于 UUID3、UUID5）
     *                           当 $type 为 3 或 5 时需要提供此数据。
     *
     * @return string 返回生成的 UUID 字符串或整数。
     *      - 如果 $number 为 true，返回整数形式的 UUID。
     *      - 如果 $number 为 false，返回标准字符串形式的 UUID。
     */
    function uuid(int $type = 0, bool $number = false, int|string $data = ''): string
    {
        // 雪花算法
        if ($type === 0) {
            $snowflake = new Snowflake();
            return $data . $snowflake->id();
        }

        // 生成不同类型的 UUID
        $uuid = match ($type) {
            1 => Uuid::uuid1(),
            3 => Uuid::uuid3(Uuid::NAMESPACE_DNS, $data),
            4 => Uuid::uuid4(),
            5 => Uuid::uuid5(Uuid::NAMESPACE_DNS, $data),
            6 => Uuid::uuid6(),
            default => Uuid::uuid7(),
        };

        // 根据是否要求数字形式返回 UUID
        return $number ? $uuid->getInteger()->toString() : $uuid->toString();
    }
}
