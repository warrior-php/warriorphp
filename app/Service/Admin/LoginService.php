<?php

namespace App\Service\Admin;

use App\Model\Admin as AdminModel;
use Exception;
use support\exception\BusinessException;
use support\Log;
use support\Redis;

class LoginService
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
     * 登录主流程
     *
     * @param array $params
     *
     * @return void
     * @throws Exception
     */
    public function login(array $params): void
    {
        $ip = request()->getRealIp();
        $attemptsKey = 'admin_login:attempts:' . $ip;
        // 登录失败次数检查
        if (Redis::get($attemptsKey) >= $this->maxAttempts) {
            throw new BusinessException(message: trans('key7'));
        }
        // 图形验证码校验
        $captcha = mb_strtolower($params['captcha'] ?? '');
        $sessionCaptcha = mb_strtolower(session('admin-login-captcha') ?? '');
        session()->delete('admin-login-captcha');
        if ($captcha !== $sessionCaptcha) {
            throw new BusinessException(message: trans('key8'));
        }
        $admin = AdminModel::where('username', $params['username'])->first();
        // 密码校验失败：记录尝试次数
        if (!$admin || !password_verify($params['password'], $admin->password)) {
            $attempts = Redis::incr($attemptsKey);
            Redis::expire($attemptsKey, $this->blockTime);
            Log::warning(trans('key9'), ['username' => $params['username'], 'ip' => $ip, 'attempts' => $attempts]);
            throw new BusinessException(message: trans('key9'));
        }
        // 更新登录记录
        $admin->login_at = date('Y-m-d H:i:s');
        $admin->login_ip = $ip;
        $admin->save();
        // 仅保存必要字段到 session
        session()->set('admin', [
            'id'       => $admin->id,
            'username' => $admin->username,
            'mobile'   => $admin->mobile,
            'email'    => $admin->email,
            'nickname' => $admin->nickname,
            'login_ip' => $admin->login_ip,
            'login_at' => $admin->login_at
        ]);
        // 登录成功：重置失败计数
        Redis::del($attemptsKey);
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

    /**
     * 退出
     * @return void
     * @throws Exception
     */
    public function logout(): void
    {
        session()->delete('admin');
    }
}