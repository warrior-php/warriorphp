<?php

namespace App\Service;

use App\Exception\BusinessException;
use Exception;
use ReflectionClass;
use ReflectionException;
use support\Redis;

class AuthService
{
    /**
     * @var string 会话加密密钥
     */
    public string $sessionKey;

    /**
     * 最大登录尝试次数
     *
     * @var int
     */
    private int $maxAttempts = 3;

    /**
     * 登录锁定时间（秒）
     *
     * @var int
     */
    private int $blockTime = 1800;

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->sessionKey = uuid(5, false, request()->host() . 'admin_session_key');
    }

    /**
     * 管理员登录
     *
     * @param array $params
     *
     * @return void
     */
    public function login(array $params): void
    {
        $ip = request()->getRealIp();
        $attemptsKey = 'admin_login:attempts:' . $ip;
        // 登录失败次数检查
        if ((int)Redis::get($attemptsKey) >= $this->maxAttempts) {
            throw new BusinessException(trans('admin.account.login.key010'));
        }
    }

    /**
     * @param string $controller
     * @param string $action
     * @param int    $code
     * @param string $msg
     *
     * @return bool
     * @throws ReflectionException|Exception
     */
    public static function canAccess(string $controller, string $action, int &$code = 0, string &$msg = ''): bool
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

        // 获取登录信息
        $account = self::getCurrentAccount();
        if (!$account) {
            $msg = '请登录';
            $code = 401; // 401是未登录固定的返回码
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
        // 如果你有刷新逻辑，可以在这里调用：
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