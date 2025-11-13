<?php

namespace App\Model;

use App\BaseModel;

/**
 * 权限规则
 * @property integer $id             ID(主键)
 * @property string  $title          标题
 * @property string  $icon           图标
 * @property string  $key            标识
 * @property integer $pid            父级
 * @property string  $href           Url
 * @property integer $type           类型
 * @property integer $weight         排序
 * @property string  $created_at     创建时间
 * @property string  $updated_at     更新时间
 */
class Rule extends BaseModel
{
    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'rule';

    /**
     * 允许批量赋值的字段
     *
     * @var array
     */
    protected $fillable = ['title', 'icon', 'key', 'pid', 'href', 'type', 'weight'];
}