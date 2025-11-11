<?php

namespace App\Model;

use support\Model;

/**
 * 角色表
 * @property integer $id                 ID(主键)
 * @property integer $role_id            角色id
 * @property integer $admin_id           管理员id
 */
class AdminRole extends Model
{
    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'admin_role';

    /**
     * 允许批量赋值的字段
     *
     * @var array
     */
    protected $fillable = ['role_id', 'admin_id'];
}