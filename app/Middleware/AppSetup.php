<?php
declare(strict_types=1);

namespace App\Middleware;

use Exception;
use support\exception\BusinessException;
use support\View;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

class AppSetup implements MiddlewareInterface
{
    /**
     * 系统版本
     *
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
        $this->initLanguage($request);
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

        // 共享全局视图变量
        View::assign([
            'languages' => getAvailableTranslations('resource/translations'),
        ]);

        return $handler($request);
    }

    /**
     * 初始化语言
     *
     * @param $request
     *
     * @return void
     * @throws Exception
     */
    private function initLanguage($request): void
    {
        // 设语言
        $language = session('lang') ?: setupLocale($request);
        locale(str_replace('-', '_', $language));
    }

    /**
     * 初始化常量
     *
     * @return void
     */
    private function initPathConst(): void
    {
        defined('DS') or define('DS', DIRECTORY_SEPARATOR);
        defined('VERSION') or define('VERSION', self::$version);
        defined('STATUS_CODE') or define('STATUS_CODE', [
            // 错误码定义
            0   => 'key2',
            200 => 'key3',
            202 => 'key3',
            204 => 'key3',
            302 => 'key4',
            401 => 'key5',
            403 => 'key6'
        ]);
    }
}