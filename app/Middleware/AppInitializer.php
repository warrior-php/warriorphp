<?php

namespace App\Middleware;

use App\Exception\BusinessException;
use Exception;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

class AppInitializer implements MiddlewareInterface
{
    /**
     * 系统版本
     * @var string
     */
    protected static string $version = 'v2.7.0';

    /**
     * 处理请求
     *
     * @param Request  $request
     * @param callable $handler
     *
     * @return Response
     * @throws Exception
     */
    public function process(Request $request, callable $handler): Response
    {
        $this->initPathConst();
        $this->initLanguage();
        // 检查是否正常安装
        $isInstalled = file_exists(base_path('/resource/install.lock'));
        $controller = request()->controller ?? '';
        $isInstallController = str_contains($controller, '\\Install\\');
        // 未安装 & 非安装控制器 -> 重定向安装
        if (!$isInstalled && !$isInstallController) {
            return redirect(url('install.index'));
        }
        // 已安装 & 是安装控制器 -> 抛出异常阻止重复安装
        if ($isInstalled && $isInstallController) {
            throw new BusinessException(message: trans("The system has been installed. To reinstall, delete the resource/install.lock file."));
        }
        return $handler($request);
    }

    /**
     * 初始化语言
     * @return void
     * @throws Exception
     */
    private function initLanguage(): void
    {
        // 设语言
        $language = session('lang') ?: setupLocale();
        locale(str_replace('-', '_', $language));
    }

    /**
     * 初始化常量
     * @return void
     */
    private function initPathConst(): void
    {
        defined('DS') or define('DS', DIRECTORY_SEPARATOR);
        defined('VERSION') or define('VERSION', self::$version);
    }
}