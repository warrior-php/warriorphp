<?php

namespace App\Model;

use App\Exception\BusinessException;
use support\Model;

/**
 * @property integer $id       ID(主键)
 * @property string  $account  用户名
 * @property string  $nickname 昵称
 * @property string  $password 密码（已加密）
 * @property string  $email    邮箱
 * @property int     $phone    手机
 * @property integer $status   状态 0正常 1禁用
 */
class Admin extends Model
{
    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'admin';

    /**
     * 允许批量赋值的字段
     *
     * @var array
     */
    protected $fillable = ['account', 'nickname', 'password', 'email', 'mobile'];

    /**
     * 自动加密密码
     *
     * @param string $value
     */
    public function setPasswordAttribute(string $value): void
    {
        // 防止重复加密
        if ($value && !password_get_info($value)['algo']) {
            $this->attributes['password'] = password_hash($value, PASSWORD_BCRYPT);
        } else {
            $this->attributes['password'] = $value;
        }
    }

    /**
     * 根据标识符查找用户（邮箱 / ID / 用户名 / 手机号）
     *
     * @param string $identifier 用户标识符
     *
     * @return Admin|null
     * @throws BusinessException
     */
    public static function findByIdentifier(string $identifier): ?self
    {
        $identifier = trim($identifier);
        if ($identifier === '') {
            throw new BusinessException(message: trans("Unknown Error"));
        }

        // 构建基础查询：只查询激活用户
        $query = self::where('status', 1);

        // 判断标识符类型
        $type = self::detectIdentifierType($identifier);

        return match ($type) {
            'email' => $query->where('email', $identifier)->first(),
            'id' => $query->find($identifier),
            'phone' => $query->where('phone', $identifier)->first(),
            'account' => $query->where('account', $identifier)->first(),
        };
    }

    /**
     * 检测标识符类型
     *
     * @param string $identifier
     *
     * @return string 'email' | 'id' | 'phone' | 'account'
     */
    protected static function detectIdentifierType(string $identifier): string
    {
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        }

        if (preg_match('/^\d{10,15}$/', $identifier)) {
            return 'phone'; // 简单手机号规则，可按需调整
        }

        if (is_numeric($identifier)) {
            return 'id';
        }

        return 'account';
    }

    /**
     * Email 是否存在
     *
     * @param string $email
     *
     * @return bool
     */
    public static function hasEmail(string $email): bool
    {
        if (self::where('email', $email)->exists()) {
            return true;
        }

        return false;
    }
}