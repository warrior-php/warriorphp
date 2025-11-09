<?php
declare(strict_types=1);

namespace App\Service;

use Exception;
use ReflectionClass;
use ReflectionException;
use support\exception\BusinessException;

/**
 * Class Auth
 *
 * 登录服务类
 * 负责验证码校验、Redis登录限制、会话保存等逻辑。
 */
class Auth
{
    /**
     * @param string $controller
     * @param string $action
     * @param int    $code
     * @param string $msg
     * @param string $loginUrl
     *
     * @return bool
     * @throws ReflectionException
     * @throws Exception
     */
    public static function canAccess(string $controller, string $action, int &$code = 0, string &$msg = '', string &$loginUrl = ''): bool
    {
        // 无控制器信息说明是函数调用，函数不属于任何控制器，鉴权操作应该在函数内部完成。
        if (!$controller) {
            return true;
        }

        // 获取控制器鉴权信息
        $class = new ReflectionClass($controller);
        $properties = $class->getDefaultProperties();
        $noNeedLogin = $properties['noNeedLogin'] ?? [];
        $noNeedAuth = $properties['noNeedAuth'] ?? [];

        // 不需要登录
        if (in_array($action, $noNeedLogin)) {
            return true;
        }
        $sessionKey = match (true) {
            str_contains($controller, 'Admin') => 'admin',
            str_contains($controller, 'Api') => 'api',
            default => 'user',
        };
        // 获取登录信息
        $account = self::getCurrentAccount(null, $sessionKey);
        if (!$account) {
            $msg = trans('key5');
            $code = 401;
            switch ($sessionKey) {
                case 'admin';
                    $loginUrl = url('admin.login');
                    break;
                case 'api';
                    throw new BusinessException(message: trans('key28'));
                default:
                    $loginUrl = url('user.login');
            }
            return false;
        }

        return true;
    }

    /**
     * 获取当前登录账号信息
     *
     * @param null|array|string $fields 指定要返回的字段（可为单个或多个）
     * @param string            $key    Session 键名（默认 admin，可扩展为 user、merchant 等）
     *
     * @return array|mixed|null 如果未登录返回 null
     * @throws Exception
     *
     * @example
     * ```php
     * // 获取完整信息
     * $admin = Auth::getCurrentAccount();
     *
     * // 获取单个字段
     * $id = Auth::getCurrentAccount('id');
     *
     * // 获取多个字段
     * $info = Auth::getCurrentAccount(['id', 'username']);
     * ```
     */
    public static function getCurrentAccount(null|array|string $fields = null, string $key = 'admin'): mixed
    {
        self::refreshSession($key);
        $account = session($key);
        if (!$account) {
            return null;
        }
        // 返回完整数据
        if ($fields === null) {
            return $account;
        }
        // 返回多个字段
        if (is_array($fields)) {
            return array_map(fn($field) => $account[$field] ?? null, $fields);
        }
        // 返回单个字段
        return $account[$fields] ?? null;
    }

    /**
     * 刷新 Session 生命周期
     *
     * @param string $key
     *
     * @return void
     * @throws Exception
     */
    protected static function refreshSession(string $key = 'admin'): void
    {

    }
}