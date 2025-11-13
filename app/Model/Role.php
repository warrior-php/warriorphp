<?php
declare(strict_types=1);

namespace App\Model;

use Exception;
use extend\Utils\Tree;

/**
 * 角色表
 * @property integer $id              ID(主键)
 * @property string  $name            角色组名称
 * @property string  $rules           权限
 * @property integer $pid             父级
 * @property string  $created_at      创建时间
 * @property string  $updated_at      更新时间
 */
class Role extends BaseModel
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

    /**
     * 获取权限范围内的所有角色id
     *
     * @param bool $with_self
     *
     * @return array
     * @throws Exception
     */
    public static function getScopeRoleIds(bool $with_self = false): array
    {
        if (!$admin = session('admin')) {
            return [];
        }
        $role_ids = $admin['roles'];
        $rules = Role::whereIn('id', $role_ids)->pluck('rules')->toArray();
        if ($rules && in_array('*', $rules)) {
            return Role::pluck('id')->toArray();
        }

        $roles = Role::get();
        $tree = new Tree($roles);
        $descendants = $tree->getDescendant($role_ids, $with_self);
        return array_column($descendants, 'id');
    }
}