<?php
declare(strict_types=1);

namespace App\Model;

/**
 * 角色表
 * @property integer $id              ID(主键)
 * @property string  $name            角色组名称
 * @property string  $rules           权限
 * @property integer $pid             父级
 * @property string  $created_at      创建时间
 * @property string  $updated_at      更新时间
 */
class RoleModel extends BaseModel
{
    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'role';

    /**
     * 允许批量赋值的字段
     *
     * @var array
     */
    protected $fillable = ['name', 'rules', 'pid'];
}