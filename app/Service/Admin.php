<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\Admin as AdminModel;
use Exception;
use support\exception\BusinessException;
use support\Log;
use support\Redis;

class Admin extends Authorize
{
    /**
     * 管理员登录
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
     * 退出登录
     * @return void
     * @throws Exception
     */
    public function logout(): void
    {
        session()->delete('admin');
    }

}