<?php

namespace App\Service\Auth;

interface AuthInterface
{
    /**
     * 登录操作
     *
     * @param array $params 登录参数（如 username/password 或 email/password）
     *
     * @return void
     */
    public function login(array $params): void;

    /**
     * 退出登录
     *
     * @return void
     */
    public function logout(): void;

    /**
     * 获取当前登录的会话数据
     *
     * @return array|null
     */
    public function getSessionData(): ?array;

    /**
     * 刷新会话（可选，用于延长登录状态）
     *
     * @return void
     */
    public function refreshSession(): void;
}