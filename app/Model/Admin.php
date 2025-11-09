<?php
declare(strict_types=1);

namespace App\Model;

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

}