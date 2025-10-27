<?php
declare(strict_types=1);
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
     * 初始化语言环境，并设置到 session 中
     *
     * @return string
     * @throws Exception
     */
    function setupLocale(): string
    {
        $language = config('translation.locale', 'en');
        // 标准化语言代码
        $language = $language === 'zh' ? 'zh-CN' : $language;
        session()->set('lang', $language);
        return $language;
    }
}