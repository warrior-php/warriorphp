<?php
declare(strict_types=1);

namespace App\Service\Auth;

use Exception;
use support\exception\BusinessException;

/**
 * Class Access
 *
 * 登录服务类
 * 负责验证码校验、Redis登录限制、会话保存等逻辑。
 */
class Access
{
    /**
     * @param string $controller
     * @param int    $code
     * @param string $msg
     * @param string $url
     * @param null   $account
     *
     * @return bool
     * @throws Exception
     */
    public static function canAccess(string $controller, int &$code = 0, string &$msg = '', string &$url = '', &$account = null): bool
    {
        // 无控制器信息说明是函数调用，函数不属于任何控制器，鉴权操作应该在函数内部完成。
        if (!$controller) {
            return true;
        }

        $sessionKey = match (true) {
            str_contains($controller, 'Admin') => 'admin',
            str_contains($controller, 'Api') => 'api',
            default => 'user',
        };
        // 获取登录信息
        $account = session($sessionKey);
        if (!$account) {
            $msg = trans('key5');
            $code = 401;
            switch ($sessionKey) {
                case 'admin';
                    $url = url('admin.login');
                    break;
                case 'api';
                    throw new BusinessException(message: trans('key28'));
                default:
                    $url = url('user.login');
            }
            return false;
        }

        return true;
    }

}