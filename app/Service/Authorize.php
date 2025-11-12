<?php
declare(strict_types=1);

namespace App\Service;

use App\Route as RouteAttr;
use Exception;
use ReflectionException;
use ReflectionMethod;
use support\exception\BusinessException;

/**
 * Class Authorize
 *
 * 权限服务类
 */
class Authorize
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
            $sessionKey = match (true) {
                str_contains($controller, 'Admin') => 'admin',
                str_contains($controller, 'Api') => 'api',
                default => 'user',
            };
            $account = self::getSessionData($sessionKey); // 获取登录信息
            switch ($sessionKey) {
                case 'user';
                    $redirectUrl = url('user.login');
                    break;
                case 'admin';
                    $redirectUrl = url('admin.account.login');
//                    $roles = $account['roles']; // 当前管理员无角色
                    break;
                default:
                    throw new BusinessException(message: trans('key28'));

            }

            if (!$account) {
                $msg = trans('key5');
                $code = 401;
                return false;
            }
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
     * @return void
     */
    public function refreshSession(): void
    {
        // 根据需要实现，例如重新生成sessionKey或延长有效期
    }

}