<?php
declare(strict_types=1);

namespace App\Model;

use support\Model;

/**
 * 管理员表
 * @property integer $id             ID(主键)
 * @property string  $username       用户名
 * @property string  $password       密码
 * @property string  $nickname       昵称
 * @property string  $avatar         头像
 * @property string  $email          邮箱
 * @property string  $mobile         手机
 * @property integer $status         状态 0正常 1禁用
 * @property string  $login_ip       最后登录IP
 * @property string  $login_at       最后登录时间
 * @property string  $created_at     创建时间
 * @property string  $updated_at     更新时间
 * @property string  $deleted_at     删除时间
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
    protected $fillable = ['username', 'password', 'nickname', 'avatar', 'email', 'mobile', 'status', 'login_ip', 'login_at'];

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