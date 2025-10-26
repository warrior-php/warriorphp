<?php

namespace App\Repositories;

use App\Model\Roles;
use Illuminate\Support\Collection;

/**
 * Class AuthRepositories
 *
 * 该类负责与「角色与权限」相关的数据操作。
 * 主要用于从数据库中根据角色 ID 获取对应的权限规则（rules）。
 *
 * 通常在业务逻辑层（Service）中调用本仓储类，
 * 以实现角色权限的查询、组合与验证等功能。
 *
 * @package App\Repositories
 */
class AuthRepositories
{
    /**
     * 根据角色 ID 列表获取对应的权限规则字段
     *
     * 此方法会从 Roles 表中查询指定角色 ID 的记录，
     * 并提取每个角色对应的 `rules` 字段内容。
     *
     * @param int|int[] $roles 角色 ID（支持单个或多个 ID）
     *
     * @return Collection 返回包含权限规则的集合
     *
     * @example
     * ```php
     * $repo = new AuthRepositories();
     * $rules = $repo->rules([1, 2, 3]);
     * // 返回类似 Collection(['user.view', 'user.edit', 'order.manage'])
     * ```
     */
    public function rules(int|array $roles): Collection
    {
        return Roles::whereIn('id', (array)$roles)->pluck('rules');
    }
}