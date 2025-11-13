<?php
declare(strict_types=1);

namespace App\Service;

use App\Route as RouteAttr;
use Exception;
use ReflectionException;
use ReflectionMethod;

/**
 * Class AdminAuthorize
 *
 * 权限服务类
 */
class AdminAuthorize
{
    /**
     * 最大登录尝试次数
     * @var int
     */
    protected int $maxAttempts = 5;

    /**
     * 登录失败封禁时间（秒）
     * @var int
     */
    protected int $blockTime = 300;

    /**
     * @param string $controller
     * @param string $action
     * @param int    $code
     * @param string $msg
     * @param string $redirectUrl
     * @param null   $account
     *
     * @return bool
     * @throws ReflectionException|Exception
     */
    public static function access(string $controller, string $action, int &$code = 0, string &$msg = '', string &$redirectUrl = '', &$account = null): bool
    {
        // 无控制器信息说明是函数调用，函数不属于任何控制器，鉴权操作应该在函数内部完成。
        if (!$controller) {
            return true;
        }
        $ref = new ReflectionMethod($controller, $action);
        $attributes = $ref->getAttributes(RouteAttr::class);

        /** @var RouteAttr $routeAttr */
        $routeAttr = $attributes[0]->newInstance();
        $permissionCode = $routeAttr->permission ?? null;
        // 执行权限验证逻辑
        if ($permissionCode) {
            $account = self::getSessionData('admin');
            if (!$account) {
                $msg = trans('key5');
                $code = 401;
                $redirectUrl = url('admin.account.login');
                return false;
            }
            $roles = '';
        }

        return true;
    }

    /**
     * 获取用户 Session 数据
     *
     * @param string $sessionKey
     *
     * @return array|null
     * @throws Exception
     */
    public static function getSessionData(string $sessionKey): ?array
    {
        return session($sessionKey) ?: null;
    }


    /**
     * 刷新会话
     *
     * @param string $sessionKey
     * @param bool   $force
     *
     * @return void|null
     * @throws Exception
     */
    public function refreshSession(string $sessionKey, bool $force = false)
    {
        $sessionData = session($sessionKey);
        if (!$sessionData) {
            return null;
        }
        $id = $sessionData['id'];
        $time_now = time();
        // session在2秒内不刷新
        $session_ttl = 2;
        $session_last_update_time = session('admin.session_last_update_time', 0);
        if (!$force && $time_now - $session_last_update_time < $session_ttl) {
            return null;
        }
        $session = request()->session();
    }

}