<?php
declare(strict_types=1);

namespace App\Model;

use Exception;

/**
 * 角色表
 * @property integer $id                 ID(主键)
 * @property integer $role_id            角色id
 * @property integer $admin_id           管理员id
 */
class AdminRole extends BaseModel
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

    /**
     * 获取权限范围内的所有管理员id
     *
     * @param bool $with_self
     *
     * @return array
     * @throws Exception
     */
    public static function getScopeAdminIds(bool $with_self = false): array
    {
        $role_ids = Role::getScopeRoleIds();
        $admin_ids = AdminRole::whereIn('role_id', $role_ids)->pluck('admin_id')->toArray();
        if ($with_self) {
            // TODO 管理员id
            $admin_ids[] = admin_id();
        }
        return array_unique($admin_ids);
    }
}