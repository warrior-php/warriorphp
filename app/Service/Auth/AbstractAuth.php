<?php

namespace App\Service\Auth;

use Exception;
use extend\Utils\DataCipher;

abstract class AbstractAuth implements AuthInterface
{
    /**
     * 会话加密密钥
     * @var string
     */
    protected string $sessionKey;

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

    public function __construct(string $sessionKey)
    {
        $this->sessionKey = $sessionKey;
    }

    /**
     * Session 加密存储
     *
     * @param string $key
     * @param array  $data
     *
     * @return void
     * @throws Exception
     */
    protected function encryptSession(string $key, array $data): void
    {
        $encrypted = DataCipher::encryptDecrypt(json_encode($data), $this->sessionKey);
        session()->set($key, $encrypted);
    }

    /**
     * Session 解密读取
     *
     * @param string $key
     *
     * @return array|null
     * @throws Exception
     */
    protected function decryptSession(string $key): ?array
    {
        $encrypted = session($key);
        if (!$encrypted) {
            return null;
        }
        $decrypted = DataCipher::encryptDecrypt($encrypted, $this->sessionKey);
        return json_decode($decrypted, true);
    }
}